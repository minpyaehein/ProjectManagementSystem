<?php

// Required imports:
use App\Models\Customer;
use App\Models\Project;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

public function edit(User $user): View
{
    $roles = Role::query()->where('status', 'Active')->orderBy('name')->get();
    $projects = Project::query()->orderBy('name')->get(['project_id', 'name']);
    $customers = Customer::query()->orderBy('name')->get(['customer_id', 'name']);
    $suppliers = Supplier::query()->orderBy('name')->get(['supplier_id', 'name']);

    $selectedProjectIds = collect(old('project_ids', $user->assignedProjects()->pluck('project.project_id')->all()))->map(fn ($id): int => (int) $id)->all();
    $selectedCustomerIds = collect(old('customer_ids', $user->assignedCustomers()->pluck('customers.customer_id')->all()))->map(fn ($id): int => (int) $id)->all();
    $selectedSupplierIds = collect(old('supplier_ids', $user->assignedSuppliers()->pluck('suppliers.supplier_id')->all()))->map(fn ($id): int => (int) $id)->all();

    return view('user-edit', compact('user', 'roles', 'projects', 'customers', 'suppliers', 'selectedProjectIds', 'selectedCustomerIds', 'selectedSupplierIds'));
}

public function update(Request $request, User $user): RedirectResponse
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
        'phone' => ['nullable', 'string', 'max:30'],
        'role_id' => ['required', 'integer', Rule::exists('role', 'role_id')],
        'password' => ['nullable', 'confirmed', 'min:8'],
        'status' => ['required', Rule::in(['Active', 'Inactive'])],
        'project_ids' => ['nullable', 'array'],
        'project_ids.*' => ['integer', 'distinct', Rule::exists('project', 'project_id')],
        'customer_ids' => ['nullable', 'array'],
        'customer_ids.*' => ['integer', 'distinct', Rule::exists('customers', 'customer_id')],
        'supplier_ids' => ['nullable', 'array'],
        'supplier_ids.*' => ['integer', 'distinct', Rule::exists('suppliers', 'supplier_id')],
    ]);

    $role = Role::query()->findOrFail($validated['role_id']);

    DB::transaction(function () use ($user, $validated, $role): void {
        $data = ['name' => $validated['name'], 'email' => $validated['email'] ?? null, 'phone' => $validated['phone'] ?? null, 'role_id' => $validated['role_id'], 'status' => $validated['status']];
        if (! empty($validated['password'])) $data['password'] = Hash::make($validated['password']);
        $user->update($data);

        $user->assignedProjects()->sync($role->project_access_scope === 'assigned' ? ($validated['project_ids'] ?? []) : []);
        $user->assignedCustomers()->sync($role->customer_access_scope === 'assigned' ? ($validated['customer_ids'] ?? []) : []);
        $user->assignedSuppliers()->sync($role->supplier_access_scope === 'assigned' ? ($validated['supplier_ids'] ?? []) : []);
    });

    return redirect()->route('usersPage')->with('success', 'User updated successfully.');
}
