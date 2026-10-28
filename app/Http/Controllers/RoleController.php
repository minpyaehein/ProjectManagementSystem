<?php
namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $roles = Role::query()
            ->with('permissions')
            ->withCount('users')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        return view('roles', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::query()->orderBy('slug')->get();

        return view('role-create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRole($request);

        DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync($validated['permissions'] ?? []);
        });

        return redirect()->route('rolesPage')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        $permissions           = Permission::query()->orderBy('slug')->get();
        $selectedPermissionIds = $role->permissions()
            ->pluck('permission.permission_id')
            ->map(fn($id) => (int) $id)
            ->all();

        return view(
            'role-edit',
            compact('role', 'permissions', 'selectedPermissionIds')
        );
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $this->validateRole($request, $role);

        DB::transaction(function () use ($validated, $role) {
            $role->update([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync($validated['permissions'] ?? []);
        });

        return redirect()->route('rolesPage')
            ->with('success', 'Role updated successfully.');
    }

    public function show(Role $role): View
    {
        $role->load('permissions');
        $role->loadCount('users');

        return view('role-detail', compact('role'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return back()->with(
                'error',
                'This role cannot be deleted because users are assigned to it.'
            );
        }

        DB::transaction(function () use ($role) {
            $role->permissions()->detach();
            $role->delete();
        });

        return redirect()->route('rolesPage')
            ->with('success', 'Role deleted successfully.');
    }

    public function deactivate(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return back()->with(
                'error',
                'This role cannot be deactivated while users are assigned to it.'
            );
        }

        $role->update(['status' => 'Inactive']);

        return back()->with('success', 'Role deactivated successfully.');
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        $nameRule = Rule::unique('role', 'name');

        if ($role) {
            $nameRule->ignore($role->role_id, 'role_id');
        }

        return $request->validate([
            'name'          => ['required', 'string', 'max:255', $nameRule],
            'description'   => ['nullable', 'string', 'max:255'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => [
                'integer',
                'distinct',
                Rule::exists('permission', 'permission_id'),
            ],
        ]);
    }
}
