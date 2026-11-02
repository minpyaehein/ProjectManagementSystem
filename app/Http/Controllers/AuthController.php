<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('login');
    }

    public function login(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate(
            [
                'username' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                ],

                'password' => [
                    'required',
                    'string',
                ],
            ],
            [
                'username.required' =>
                    'Please enter your username.',

                'username.min' =>
                    'The username must contain at least 3 characters.',

                'username.max' =>
                    'The username cannot exceed 100 characters.',

                'password.required' =>
                    'Please enter your password.',
            ]
        );

        $authenticated = Auth::attempt(
            [
                'username' => strtolower(
                    trim($validated['username'])
                ),

                'password' =>
                    $validated['password'],

                'status' => 'Active',
            ],
            $request->boolean('remember')
        );

        if (! $authenticated) {
            throw ValidationException::withMessages([
                'username' =>
                    'The username or password is incorrect.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(
            route('homePage')
        );
    }

    public function logout(
        Request $request
    ): RedirectResponse {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
