<?php
namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display the searchable role list.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $roles = Role::query()
            ->withCount('users')
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
                        function (Builder $roleQuery) use ($search): void {
                            $roleQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere(
                                    'description',
                                    'like',
                                    '%' . $search . '%'
                                );
                        }
                    );
                }
            )
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('roles', compact('roles'));
    }

    /**
     * Show the role creation form.
     */
    public function create(): View
    {
        $permissions = Permission::query()
            ->orderBy('slug')
            ->get();

        return view('role-create', compact('permissions'));
    }

    /**
     * Store a new role and synchronize its permissions.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRole($request);

        $role = DB::transaction(
            function () use ($validated): Role {
                $role = Role::query()->create([
                    'name'                  => $validated['name'],
                    'description'           => $validated['description'] ?? null,
                    'customer_access_scope' =>
                    $validated['customer_access_scope'],
                    'supplier_access_scope' =>
                    $validated['supplier_access_scope'],
                    'project_access_scope'  =>
                    $validated['project_access_scope'],
                    'status'                => 'Active',
                ]);

                $role->permissions()->sync(
                    $validated['permissions'] ?? []
                );

                return $role;
            }
        );

        return redirect()
            ->route('roles.show', $role)
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display one role with permissions and user count.
     */
    public function show(Role $role): View
    {
        $role->load('permissions')->loadCount('users');

        return view('role-detail', compact('role'));
    }

    /**
     * Show the role edit form.
     */
    public function edit(Role $role): View
    {
        $permissions = Permission::query()
            ->orderBy('slug')
            ->get();

        $selectedPermissionIds = $role->permissions()
            ->pluck('permission.permission_id')
            ->map(fn($id): int => (int) $id)
            ->all();

        return view(
            'role-edit',
            compact(
                'role',
                'permissions',
                'selectedPermissionIds'
            )
        );
    }

    /**
     * Update role details, access scopes, and permissions.
     */
    public function update(
        Request $request,
        Role $role
    ): RedirectResponse {
        $validated = $this->validateRole($request, $role);

        DB::transaction(
            function () use ($role, $validated): void {
                $role->update([
                    'name'                  => $validated['name'],
                    'description'           => $validated['description'] ?? null,
                    'customer_access_scope' =>
                    $validated['customer_access_scope'],
                    'supplier_access_scope' =>
                    $validated['supplier_access_scope'],
                    'project_access_scope'  =>
                    $validated['project_access_scope'],
                ]);

                $role->permissions()->sync(
                    $validated['permissions'] ?? []
                );
            }
        );

        return redirect()
            ->route('roles.show', $role)
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Deactivate an unused role.
     */
    public function deactivate(
        Request $request,
        Role $role
    ): RedirectResponse {
        abort_unless(
            $request->user()?->hasPermission('roles.deactivate'),
            403
        );

        if (
            (int) $request->user()->role_id
            === (int) $role->role_id
        ) {
            return back()->with(
                'error',
                'You cannot deactivate your own assigned role.'
            );
        }

        if ($role->users()->exists()) {
            return back()->with(
                'error',
                'This role cannot be deactivated because users are assigned to it.'
            );
        }

        if (($role->status ?? 'Active') === 'Inactive') {
            return back()->with(
                'error',
                'Role is already inactive.'
            );
        }

        $role->update([
            'status' => 'Inactive',
        ]);

        return back()->with(
            'success',
            'Role deactivated successfully.'
        );
    }

    /**
     * Reactivate an inactive role.
     */
    public function activate(
        Request $request,
        Role $role
    ): RedirectResponse {
        abort_unless(
            $request->user()?->hasPermission('roles.deactivate'),
            403
        );

        if (($role->status ?? 'Active') === 'Active') {
            return back()->with(
                'error',
                'Role is already active.'
            );
        }

        $role->update([
            'status' => 'Active',
        ]);

        return back()->with(
            'success',
            'Role activated successfully.'
        );
    }

    /**
     * Validate shared Create and Edit fields.
     *
     * @return array<string, mixed>
     */
    private function validateRole(
        Request $request,
        ?Role $role = null
    ): array {
        return $request->validate(
            [
                'name'                  => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('role', 'name')->ignore(
                        $role?->role_id,
                        'role_id'
                    ),
                ],
                'description'           => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
                'customer_access_scope' => [
                    'required',
                    Rule::in([
                        'own',
                        'assigned',
                        'all',
                    ]),
                ],
                'supplier_access_scope' => [
                    'required',
                    Rule::in([
                        'own',
                        'assigned',
                        'all',
                    ]),
                ],
                'project_access_scope'  => [
                    'required',
                    Rule::in([
                        'own',
                        'assigned',
                        'all',
                    ]),
                ],
                'permissions'           => [
                    'nullable',
                    'array',
                ],
                'permissions.*'         => [
                    'integer',
                    'distinct',
                    Rule::exists(
                        'permission',
                        'permission_id'
                    ),
                ],
            ],
            [
                'name.required'                  => 'Please enter the role name.',
                'name.unique'                    => 'This role name already exists.',
                'customer_access_scope.required' =>
                'Please select the customer access scope.',
                'supplier_access_scope.required' =>
                'Please select the supplier access scope.',
                'project_access_scope.required'  =>
                'Please select the project access scope.',
                'permissions.*.exists'           =>
                'One of the selected permissions is invalid.',
            ]
        );
    }
}
