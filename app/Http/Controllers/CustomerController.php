<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $status = $validated['status'] ?? null;

        $customers = Customer::query()
            ->with('primaryContact')
            ->withCount('contacts')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
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
            ->when($status, function ($query, string $status): void {
                $query->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        return view('customers', compact('customers'));
    }

    public function create(): View
    {
        return view('customer-create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCustomer($request);

        DB::transaction(function () use ($validated): void {
            $customer = Customer::create([
                'name'    => $validated['name'],
                'address' => $validated['address'] ?? null,
                'status'  => $validated['status'],
                'notes'   => $validated['notes'] ?? null,
            ]);

            $this->syncContacts(
                $customer,
                $validated['contacts'],
                (int) $validated['primary_contact']
            );

            $this->syncLegacyPrimaryFields($customer);
        });

        return redirect()
            ->route('customersPage')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer): View
    {
        $customer->load('contacts');

        return view('customer-edit', compact('customer'));
    }

    public function update(
        Request $request,
        Customer $customer
    ): RedirectResponse {
        $validated = $this->validateCustomer($request);

        DB::transaction(function () use ($validated, $customer): void {
            $customer->update([
                'name'    => $validated['name'],
                'address' => $validated['address'] ?? null,
                'status'  => $validated['status'],
                'notes'   => $validated['notes'] ?? null,
            ]);

            $this->syncContacts(
                $customer,
                $validated['contacts'],
                (int) $validated['primary_contact']
            );

            $this->syncLegacyPrimaryFields($customer);
        });

        return redirect()
            ->route('customers.edit', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function deactivate(Customer $customer): RedirectResponse
    {
        $customer->update([
            'status' => 'Inactive',
        ]);

        return back()->with(
            'success',
            'Customer deactivated successfully.'
        );
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->projects()->exists()) {
            return back()->with(
                'error',
                'This customer cannot be deleted because projects are assigned to it.'
            );
        }

        $customer->delete();

        return redirect()
            ->route('customersPage')
            ->with('success', 'Customer deleted successfully.');
    }

    private function validateCustomer(Request $request): array
    {
        $contacts            = $request->input('contacts', []);
        $validPrimaryIndexes = is_array($contacts)
            ? array_keys(array_values($contacts))
            : [];

        return $request->validate(
            [
                'name'                           => [
                    'required',
                    'string',
                    'max:255',
                ],
                'address'                        => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
                'status'                         => [
                    'required',
                    Rule::in(['Active', 'Inactive']),
                ],
                'notes'                          => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
                'contacts'                       => [
                    'required',
                    'array',
                    'min:1',
                    'max:20',
                ],
                'contacts.*.customer_contact_id' => [
                    'nullable',
                    'integer',
                ],
                'contacts.*.name'                => [
                    'required',
                    'string',
                    'max:255',
                ],
                'contacts.*.position'            => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'contacts.*.phone'               => [
                    'nullable',
                    'string',
                    'max:30',
                    'regex:/^[0-9+\-\s()]+$/',
                ],
                'contacts.*.email'               => [
                    'nullable',
                    'email',
                    'max:255',
                ],
                'primary_contact'                => [
                    'required',
                    'integer',
                    Rule::in($validPrimaryIndexes),
                ],
            ],
            [
                'name.required'            => 'Please enter the customer name.',
                'status.required'          => 'Please select a customer status.',
                'contacts.required'        => 'Please add at least one contact person.',
                'contacts.min'             => 'Please add at least one contact person.',
                'contacts.max'             => 'A customer can have no more than 20 contacts.',
                'contacts.*.name.required' => 'Every contact person must have a name.',
                'contacts.*.phone.regex'   =>
                'A contact phone number may contain only numbers, spaces, +, -, and parentheses.',
                'contacts.*.email.email'   =>
                'A contact email address is invalid.',
                'primary_contact.required' =>
                'Please select a primary contact.',
                'primary_contact.in'       =>
                'The selected primary contact is invalid.',
            ]
        );
    }

    private function syncContacts(
        Customer $customer,
        array $contacts,
        int $primaryIndex
    ): void {
        $keptContactIds = [];

        foreach (array_values($contacts) as $index => $contactData) {
            $contactId = ! empty($contactData['customer_contact_id'])
                ? (int) $contactData['customer_contact_id']
                : null;

            $payload = [
                'name'       => trim($contactData['name']),
                'position'   => $this->nullableTrim(
                    $contactData['position'] ?? null
                ),
                'phone'      => $this->nullableTrim(
                    $contactData['phone'] ?? null
                ),
                'email'      => $this->nullableTrim(
                    $contactData['email'] ?? null
                ),
                'is_primary' => $index === $primaryIndex,
            ];

            if ($contactId !== null) {
                $contact = $customer->contacts()
                    ->where('customer_contact_id', $contactId)
                    ->first();

                if ($contact) {
                    $contact->update($payload);
                    $keptContactIds[] = $contact->customer_contact_id;
                    continue;
                }
            }

            $contact          = $customer->contacts()->create($payload);
            $keptContactIds[] = $contact->customer_contact_id;
        }

        $customer->contacts()
            ->whereNotIn('customer_contact_id', $keptContactIds)
            ->delete();
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'contacts',
        ]);

        return view(
            'customer-detail',
            compact('customer')
        );
    }

    private function syncLegacyPrimaryFields(Customer $customer): void
    {
        $primaryContact = $customer->contacts()
            ->where('is_primary', true)
            ->first();

        $customer->update([
            'contact_person' => $primaryContact?->name,
            'phone'          => $primaryContact?->phone,
            'email'          => $primaryContact?->email,
        ]);
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
