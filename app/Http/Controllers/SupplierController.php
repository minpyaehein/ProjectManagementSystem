<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupplierController extends Controller
{
    private const CATEGORIES = [
        'Construction',
        'Office equipment',
        'Electrical',
        'IT services',
        'Other',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $status = $validated['status'] ?? null;

        $suppliers = Supplier::query()
            ->with('primaryContact')
            ->withCount('contacts')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('category', 'like', '%' . $search . '%')
                        ->orWhere('contact_person', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%')
                        ->orWhereHas('contacts', function ($contactQuery) use ($search): void {
                            $contactQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('position', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($status, fn($query, string $status) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        return view('suppliers', compact('suppliers'));
    }

    public function create(): View
    {
        $categories = self::CATEGORIES;

        return view('supplier-create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSupplier($request);

        DB::transaction(function () use ($validated): void {
            $supplier = Supplier::create([
                'name'     => $validated['name'],
                'category' => $validated['category'],
                'address'  => $validated['address'] ?? null,
                'status'   => $validated['status'],
                'notes'    => $validated['notes'] ?? null,
            ]);

            $this->syncContacts($supplier, $validated['contacts'], (int) $validated['primary_contact']);
            $this->syncLegacyPrimaryFields($supplier);
        });

        return redirect()->route('suppliersPage')->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load('contacts');

        return view('supplier-detail', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        $supplier->load('contacts');
        $categories = self::CATEGORIES;

        return view('supplier-edit', compact('supplier', 'categories'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $this->validateSupplier($request);

        DB::transaction(function () use ($validated, $supplier): void {
            $supplier->update([
                'name'     => $validated['name'],
                'category' => $validated['category'],
                'address'  => $validated['address'] ?? null,
                'status'   => $validated['status'],
                'notes'    => $validated['notes'] ?? null,
            ]);

            $this->syncContacts($supplier, $validated['contacts'], (int) $validated['primary_contact']);
            $this->syncLegacyPrimaryFields($supplier);
        });

        return redirect()->route('suppliersPage')->with('success', 'Supplier updated successfully.');
    }

    public function deactivate(Supplier $supplier): RedirectResponse
    {
        $supplier->update(['status' => 'Inactive']);

        return back()->with('success', 'Supplier deactivated successfully.');
    }

    public function activate(
        Request $request,
        Supplier $supplier
    ): RedirectResponse {
        abort_unless(
            $request->user()->hasPermission(
                'suppliers.deactivate'
            ),
            403
        );

        if ($supplier->status === 'Active') {
            return redirect()
                ->route('suppliersPage')
                ->with(
                    'error',
                    'Supplier is already active.'
                );
        }

        $supplier->update([
            'status' => 'Active',
        ]);

        return redirect()
            ->route('suppliersPage')
            ->with(
                'success',
                'Supplier activated successfully.'
            );
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->projects()->exists()) {
            return back()->with('error', 'This supplier cannot be deleted because projects are assigned to it.');
        }

        $supplier->delete();

        return redirect()->route('suppliersPage')->with('success', 'Supplier deleted successfully.');
    }

    private function validateSupplier(Request $request): array
    {
        $contacts            = $request->input('contacts', []);
        $validPrimaryIndexes = is_array($contacts) ? array_keys(array_values($contacts)) : [];

        return $request->validate([
            'name'                           => ['required', 'string', 'max:255'],
            'category'                       => ['required', Rule::in(self::CATEGORIES)],
            'address'                        => ['nullable', 'string', 'max:1000'],
            'status'                         => ['required', Rule::in(['Active', 'Inactive'])],
            'notes'                          => ['nullable', 'string', 'max:2000'],
            'contacts'                       => ['required', 'array', 'min:1', 'max:20'],
            'contacts.*.supplier_contact_id' => ['nullable', 'integer'],
            'contacts.*.name'                => ['required', 'string', 'max:255'],
            'contacts.*.position'            => ['nullable', 'string', 'max:255'],
            'contacts.*.phone'               => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'contacts.*.email'               => ['nullable', 'email', 'max:255'],
            'primary_contact'                => ['required', 'integer', Rule::in($validPrimaryIndexes)],
        ], [
            'name.required'            => 'Please enter the supplier name.',
            'category.required'        => 'Please select a supplier category.',
            'contacts.required'        => 'Please add at least one contact person.',
            'contacts.min'             => 'Please add at least one contact person.',
            'contacts.*.name.required' => 'Every contact person must have a name.',
            'contacts.*.phone.regex'   => 'A contact phone number contains invalid characters.',
            'contacts.*.email.email'   => 'A contact email address is invalid.',
            'primary_contact.required' => 'Please select a primary contact.',
            'primary_contact.in'       => 'The selected primary contact is invalid.',
        ]);
    }

    private function syncContacts(Supplier $supplier, array $contacts, int $primaryIndex): void
    {
        $keptIds = [];

        foreach (array_values($contacts) as $index => $contactData) {
            $contactId = ! empty($contactData['supplier_contact_id'])
                ? (int) $contactData['supplier_contact_id']
                : null;

            $payload = [
                'name'       => trim($contactData['name']),
                'position'   => $this->nullableTrim($contactData['position'] ?? null),
                'phone'      => $this->nullableTrim($contactData['phone'] ?? null),
                'email'      => $this->nullableTrim($contactData['email'] ?? null),
                'is_primary' => $index === $primaryIndex,
            ];

            if ($contactId !== null) {
                $contact = $supplier->contacts()->where('supplier_contact_id', $contactId)->first();

                if ($contact) {
                    $contact->update($payload);
                    $keptIds[] = $contact->supplier_contact_id;
                    continue;
                }
            }

            $contact   = $supplier->contacts()->create($payload);
            $keptIds[] = $contact->supplier_contact_id;
        }

        $supplier->contacts()->whereNotIn('supplier_contact_id', $keptIds)->delete();
    }

    private function syncLegacyPrimaryFields(Supplier $supplier): void
    {
        $primary = $supplier->contacts()->where('is_primary', true)->first();

        $supplier->update([
            'contact_person' => $primary?->name,
            'phone'          => $primary?->phone,
            'email'          => $primary?->email,
        ]);
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
