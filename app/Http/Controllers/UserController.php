<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $roleId = $request->input('role_id');
        $status = $request->input('status');

        $users = User::query()
            ->with('role')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhereHas('role', function ($roleQuery) use ($search): void {
                            $roleQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($roleId, function ($query) use ($roleId): void {
                $query->where('role_id', $roleId);
            })
            ->when($status, function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return view('users', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return view('user-create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users', 'name'),
                ],
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email'),
                ],
                'phone' => [
                    'nullable',
                    'string',
                    'max:30',
                    'regex:/^[0-9+\-\s()]+$/',
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],
                'role_id' => [
                    'required',
                    'integer',
                    Rule::exists('role', 'role_id'),
                ],
                'status' => [
                    'required',
                    Rule::in(['Active', 'Inactive']),
                ],
            ],
            [
                'name.required' => 'Please enter the user name.',
                'name.unique' => 'This login name is already being used.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already being used.',
                'phone.regex' => 'The phone number may contain only numbers, spaces, +, -, and parentheses.',
                'password.required' => 'Please enter a password.',
                'password.min' => 'The password must contain at least 8 characters.',
                'password.confirmed' => 'The password confirmation does not match.',
                'role_id.required' => 'Please select a role.',
                'role_id.exists' => 'The selected role does not exist.',
                'status.required' => 'Please select a user status.',
            ]
        );

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('usersPage')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        $user->load([
            'role.permissions',
            'project',
        ]);

        return view('user-detail', compact('user'));
    }

    public function edit(User $user): View
    {
        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return view('user-edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users', 'name')
                        ->ignore($user->user_id, 'user_id'),
                ],
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')
                        ->ignore($user->user_id, 'user_id'),
                ],
                'phone' => [
                    'nullable',
                    'string',
                    'max:30',
                    'regex:/^[0-9+\-\s()]+$/',
                ],
                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'confirmed',
                ],
                'role_id' => [
                    'required',
                    'integer',
                    Rule::exists('role', 'role_id'),
                ],
                'status' => [
                    'required',
                    Rule::in(['Active', 'Inactive']),
                ],
            ],
            [
                'name.required' => 'Please enter the user name.',
                'name.unique' => 'This login name is already being used.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already being used.',
                'phone.regex' => 'The phone number may contain only numbers, spaces, +, -, and parentheses.',
                'password.min' => 'The password must contain at least 8 characters.',
                'password.confirmed' => 'The password confirmation does not match.',
                'role_id.required' => 'Please select a role.',
                'role_id.exists' => 'The selected role does not exist.',
                'status.required' => 'Please select a user status.',
            ]
        );

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('usersPage')
            ->with('success', 'User updated successfully.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_unless(
            $request->user()->hasPermission('users.edit')
                || $request->user()->hasPermission('users.manage'),
            403
        );

        $user->update([
            'password' => Hash::make('Default@12345'),
        ]);

        return redirect()
            ->route('users.edit', $user)
            ->with(
                'success',
                'Password reset successfully. The default password is Default@12345.'
            );
    }

    public function deactivate(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->with(
                'error',
                'You cannot deactivate your own account.'
            );
        }

        $user->update(['status' => 'Inactive']);

        return back()->with('success', 'User deactivated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->user_id) {
            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }

        $user->delete();

        return redirect()
            ->route('usersPage')
            ->with('success', 'User deleted successfully.');
    }
}
