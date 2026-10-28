<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('homePage');
        }

        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'password' => [
                    'required',
                    'string',
                ],
                'remember' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'name.required' => 'Please enter your username.',
                'password.required' => 'Please enter your password.',
            ]
        );

        $remember = $request->boolean('remember');

        $authenticated = Auth::attempt(
            [
                'name' => $credentials['name'],
                'password' => $credentials['password'],
                'status' => 'Active',
            ],
            $remember
        );

        if (!$authenticated) {
            return back()
                ->withErrors([
                    'name' =>
                        'The username or password is incorrect, or the account is inactive.',
                ])
                ->onlyInput('name', 'remember');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('homePage'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'You have been signed out successfully.');
    }
}
