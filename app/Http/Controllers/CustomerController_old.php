<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display customer records.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');

        $customers = Customer::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere(
                            'contact_person',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'email',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'phone',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'address',
                            'like',
                            '%' . $search . '%'
                        );
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        return view('customers', compact('customers'));
    }

    /**
     * Display the customer creation form.
     */
    public function create(): View
    {
        return view('customer-create');
    }

    /**
     * Store a new customer.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'contact_person' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],
                'phone' => [
                    'nullable',
                    'string',
                    'max:15',
                ],
                'address' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'status' => [
                    'required',
                    Rule::in([
                        'Active',
                        'Inactive',
                    ]),
                ],
                'notes' => [
                    'nullable',
                    'string',
                ],
            ],
            [
                'name.required' => 'Please enter the customer name.',
                'status.required' => 'Please select the customer status.',
            ]
        );

        Customer::create($validated);

        return redirect()
            ->route('customersPage')
            ->with('success', 'Customer created successfully.');
    }
}
