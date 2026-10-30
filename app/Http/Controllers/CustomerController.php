<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Support\RecordAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));

        $customers = RecordAccess::apply(Customer::query(), $request->user(), 'customer_access_scope')
            ->with('primaryContact')
            ->withCount('contacts')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($customerQuery) use ($search): void {
                    $customerQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('contact_person', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhereHas('contacts', function ($contactQuery) use ($search): void {
                            $contactQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when(in_array($status, ['Active', 'Inactive'], true), fn($query) => $query->where('status', $status))
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('customers', compact('customers'));
    }

    public function create(): View
    {
        return view('customer-create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('customers.create'), 403);
        $validated = $this->validateCustomer($request);

        DB::transaction(function () use ($validated, $request): void {
            $customer = Customer::query()->create([
                'name'             => $validated['name'],
                'address'          => $validated['address'] ?? null,
                'status'           => $validated['status'],
                'notes'            => $validated['notes'] ?? null,
                'created_by'       => $request->user()->user_id,
                'assigned_user_id' => $request->user()->user_id,
            ]);
            $this->syncContacts($customer, $validated['contacts'], (int) $validated['primary_contact']);
            $this->syncLegacyPrimaryFields($customer);
        });

        return redirect()->route('customersPage')->with('success', 'Customer created successfully.');
    }

    public function show(Request $request, Customer $customer): View
    {
        RecordAccess::authorize($request->user(), $customer, 'customer_access_scope', 'customers.view');
        $customer->load('contacts');
        return view('customer-detail', compact('customer'));
    }

    public function edit(Request $request, Customer $customer): View
    {
        RecordAccess::authorize($request->user(), $customer, 'customer_access_scope', 'customers.edit');
        $customer->load('contacts');
        return view('customer-edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        RecordAccess::authorize($request->user(), $customer, 'customer_access_scope', 'customers.edit');
        $validated = $this->validateCustomer($request);

        DB::transaction(function () use ($validated, $customer): void {
            $customer->update([
                'name'    => $validated['name'],
                'address' => $validated['address'] ?? null,
                'status'  => $validated['status'],
                'notes'   => $validated['notes'] ?? null,
            ]);
            $this->syncContacts($customer, $validated['contacts'], (int) $validated['primary_contact']);
            $this->syncLegacyPrimaryFields($customer);
        });

        return redirect()->route('customersPage')->with('success', 'Customer updated successfully.');
    }

    public function deactivate(Request $request, Customer $customer): RedirectResponse
    {
        RecordAccess::authorize($request->user(), $customer, 'customer_access_scope', 'customers.deactivate');
        $customer->update(['status' => 'Inactive']);
        return back()->with('success', 'Customer deactivated successfully.');
    }

    public function activate(Request $request, Customer $customer): RedirectResponse
    {
        RecordAccess::authorize($request->user(), $customer, 'customer_access_scope', 'customers.deactivate');
        if ($customer->status === 'Active') {
            return back()->with('error', 'Customer is already active.');
        }

        $customer->update(['status' => 'Active']);
        return back()->with('success', 'Customer activated successfully.');
    }

    public function destroy(Request $request, Customer $customer): RedirectResponse
    {
        RecordAccess::authorize($request->user(), $customer, 'customer_access_scope', 'customers.deactivate');
        if ($customer->projects()->exists()) {
            return back()->with('error', 'This customer cannot be deleted because projects are assigned to it.');
        }

        $customer->delete();
        return redirect()->route('customersPage')->with('success', 'Customer deleted successfully.');
    }

    private function validateCustomer(Request $request): array
    {
        $contacts = $request->input('contacts', []);
        $indexes  = is_array($contacts) ? array_keys(array_values($contacts)) : [];
        return $request->validate([
            'name'                           => ['required', 'string', 'max:255'],
            'address'                        => ['nullable', 'string', 'max:1000'],
            'status'                         => ['required', Rule::in(['Active', 'Inactive'])],
            'notes'                          => ['nullable', 'string', 'max:2000'],
            'contacts'                       => ['required', 'array', 'min:1', 'max:20'],
            'contacts.*.customer_contact_id' => ['nullable', 'integer'],
            'contacts.*.name'                => ['required', 'string', 'max:255'],
            'contacts.*.position'            => ['nullable', 'string', 'max:255'],
            'contacts.*.phone'               => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'contacts.*.email'               => ['nullable', 'email', 'max:255'],
            'primary_contact'                => ['required', 'integer', Rule::in($indexes)],
        ]);
    }

    private function syncContacts(Customer $customer, array $contacts, int $primaryIndex): void
    {
        $kept = [];
        foreach (array_values($contacts) as $index => $data) {
            $id      = ! empty($data['customer_contact_id']) ? (int) $data['customer_contact_id'] : null;
            $payload = [
                'name'       => trim($data['name']),
                'position'   => $this->nullableTrim($data['position'] ?? null),
                'phone'      => $this->nullableTrim($data['phone'] ?? null),
                'email'      => $this->nullableTrim($data['email'] ?? null),
                'is_primary' => $index === $primaryIndex,
            ];
            $contact = $id ? $customer->contacts()->where('customer_contact_id', $id)->first() : null;
            if ($contact) {
                $contact->update($payload);
            } else {
                $contact = $customer->contacts()->create($payload);
            }

            $kept[] = $contact->customer_contact_id;
        }
        $customer->contacts()->whereNotIn('customer_contact_id', $kept)->delete();
    }

    private function syncLegacyPrimaryFields(Customer $customer): void
    {
        $primary = $customer->contacts()->where('is_primary', true)->first();
        $customer->update(['contact_person' => $primary?->name, 'phone' => $primary?->phone, 'email' => $primary?->email]);
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
