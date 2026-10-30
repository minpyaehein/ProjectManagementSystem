<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Support\RecordAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupplierController extends Controller
{
    private const CATEGORIES = ['Construction', 'Office equipment', 'Electrical', 'IT services', 'Other'];

    public function index(Request $request): View
    {
        $validated = $request->validate(['search' => ['nullable', 'string', 'max:255'], 'status' => ['nullable', Rule::in(['Active', 'Inactive'])]]);
        $search    = trim((string) ($validated['search'] ?? ''));
        $status    = $validated['status'] ?? null;

        $suppliers = RecordAccess::apply(Supplier::query(), $request->user(), 'supplier_access_scope')
            ->with('primaryContact')->withCount('contacts')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', '%' . $search . '%')->orWhere('category', 'like', '%' . $search . '%')
                        ->orWhere('contact_person', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')->orWhere('address', 'like', '%' . $search . '%')
                        ->orWhereHas('contacts', function ($contact) use ($search): void {
                            $contact->where('name', 'like', '%' . $search . '%')->orWhere('position', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($status, fn($query, string $status) => $query->where('status', $status))
            ->orderByDesc('created_at')->paginate(5)->withQueryString();

        return view('suppliers', compact('suppliers'));
    }

    public function create(): View
    {$categories = self::CATEGORIES;return view('supplier-create', compact('categories'));}

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('suppliers.create'), 403);
        $validated = $this->validateSupplier($request);
        DB::transaction(function () use ($validated, $request): void {
            $supplier = Supplier::query()->create([
                'name'       => $validated['name'], 'category'                => $validated['category'], 'address' => $validated['address'] ?? null,
                'status'     => $validated['status'], 'notes'                 => $validated['notes'] ?? null,
                'created_by' => $request->user()->user_id, 'assigned_user_id' => $request->user()->user_id,
            ]);
            $this->syncContacts($supplier, $validated['contacts'], (int) $validated['primary_contact']);
            $this->syncLegacyPrimaryFields($supplier);
        });
        return redirect()->route('suppliersPage')->with('success', 'Supplier created successfully.');
    }

    public function show(Request $request, Supplier $supplier): View
    {RecordAccess::authorize($request->user(), $supplier, 'supplier_access_scope', 'suppliers.view');
        $supplier->load('contacts');return view('supplier-detail', compact('supplier'));}

    public function edit(Request $request, Supplier $supplier): View
    {RecordAccess::authorize($request->user(), $supplier, 'supplier_access_scope', 'suppliers.edit');
        $supplier->load('contacts');
        $categories = self::CATEGORIES;return view('supplier-edit', compact('supplier', 'categories'));}

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        RecordAccess::authorize($request->user(), $supplier, 'supplier_access_scope', 'suppliers.edit');
        $validated = $this->validateSupplier($request);
        DB::transaction(function () use ($validated, $supplier): void {
            $supplier->update(['name' => $validated['name'], 'category' => $validated['category'], 'address' => $validated['address'] ?? null, 'status' => $validated['status'], 'notes' => $validated['notes'] ?? null]);
            $this->syncContacts($supplier, $validated['contacts'], (int) $validated['primary_contact']);
            $this->syncLegacyPrimaryFields($supplier);
        });
        return redirect()->route('suppliersPage')->with('success', 'Supplier updated successfully.');
    }

    public function deactivate(Request $request, Supplier $supplier): RedirectResponse
    {RecordAccess::authorize($request->user(), $supplier, 'supplier_access_scope', 'suppliers.deactivate');
        $supplier->update(['status' => 'Inactive']);return back()->with('success', 'Supplier deactivated successfully.');}

    public function activate(Request $request, Supplier $supplier): RedirectResponse
    {RecordAccess::authorize($request->user(), $supplier, 'supplier_access_scope', 'suppliers.deactivate');if ($supplier->status === 'Active') {
        return back()->with('error', 'Supplier is already active.');
    }

        $supplier->update(['status' => 'Active']);return back()->with('success', 'Supplier activated successfully.');}

    public function destroy(Request $request, Supplier $supplier): RedirectResponse
    {RecordAccess::authorize($request->user(), $supplier, 'supplier_access_scope', 'suppliers.deactivate');if ($supplier->projects()->exists()) {
        return back()->with('error', 'This supplier cannot be deleted because projects are assigned to it.');
    }

        $supplier->delete();return redirect()->route('suppliersPage')->with('success', 'Supplier deleted successfully.');}

    private function validateSupplier(Request $request): array
    {
        $contacts = $request->input('contacts', []);
        $indexes  = is_array($contacts) ? array_keys(array_values($contacts)) : [];
        return $request->validate([
            'name'                           => ['required', 'string', 'max:255'], 'category'                                   => ['required', Rule::in(self::CATEGORIES)], 'address'      => ['nullable', 'string', 'max:1000'],
            'status'                         => ['required', Rule::in(['Active', 'Inactive'])], 'notes'                         => ['nullable', 'string', 'max:2000'], 'contacts'           => ['required', 'array', 'min:1', 'max:20'],
            'contacts.*.supplier_contact_id' => ['nullable', 'integer'], 'contacts.*.name'                                      => ['required', 'string', 'max:255'], 'contacts.*.position' => ['nullable', 'string', 'max:255'],
            'contacts.*.phone'               => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'], 'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'primary_contact'                => ['required', 'integer', Rule::in($indexes)],
        ]);
    }

    private function syncContacts(Supplier $supplier, array $contacts, int $primaryIndex): void
    {
        $kept = [];foreach (array_values($contacts) as $index => $data) {
            $id      = ! empty($data['supplier_contact_id']) ? (int) $data['supplier_contact_id'] : null;
            $payload = ['name' => trim($data['name']), 'position' => $this->nullableTrim($data['position'] ?? null), 'phone' => $this->nullableTrim($data['phone'] ?? null), 'email' => $this->nullableTrim($data['email'] ?? null), 'is_primary' => $index === $primaryIndex];
            $contact = $id ? $supplier->contacts()->where('supplier_contact_id', $id)->first() : null;
            if ($contact) {
                $contact->update($payload);
            } else {
                $contact = $supplier->contacts()->create($payload);
            }

            $kept[] = $contact->supplier_contact_id;
        }
        $supplier->contacts()->whereNotIn('supplier_contact_id', $kept)->delete();
    }

    private function syncLegacyPrimaryFields(Supplier $supplier): void
    {$primary = $supplier->contacts()->where('is_primary', true)->first();
        $supplier->update(['contact_person' => $primary?->name, 'phone' => $primary?->phone, 'email' => $primary?->email]);}
    private function nullableTrim(?string $value): ?string
    {$value = trim((string) $value);return $value === '' ? null : $value;}
}
