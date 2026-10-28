<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user()->load('role');

        return view('profile', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

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
            ],
            [
                'name.required' => 'Please enter your full name.',
                'name.unique' => 'This name is already being used.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already being used.',
                'phone.regex' =>
                    'The phone number may contain only numbers, spaces, +, -, and parentheses.',
            ]
        );

        $user->update([
            'name' => trim($validated['name']),
            'email' => $this->nullableTrim($validated['email'] ?? null),
            'phone' => $this->nullableTrim($validated['phone'] ?? null),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('profile_success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validateWithBag(
            'passwordUpdate',
            [
                'current_password' => [
                    'required',
                    'string',
                    'current_password',
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'different:current_password',
                ],
            ],
            [
                'current_password.required' =>
                    'Please enter your current password.',
                'current_password.current_password' =>
                    'The current password is incorrect.',
                'password.required' =>
                    'Please enter a new password.',
                'password.min' =>
                    'The new password must contain at least 8 characters.',
                'password.confirmed' =>
                    'The new password confirmation does not match.',
                'password.different' =>
                    'The new password must be different from the current password.',
            ]
        );

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->session()->regenerate();

        return redirect()
            ->route('profile.edit')
            ->with('password_success', 'Password changed successfully.');
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
