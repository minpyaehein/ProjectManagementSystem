<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login page.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('homePage');
        }

        return view('login');
    }

    /**
     * Process login submission.
     */
    public function authenticate(Request $request): RedirectResponse
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
            ],
            [
                'name.required' => 'Please enter your name.',
                'password.required' => 'Please enter your password.',
            ]
        );

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('homePage'));
        }

        return back()
            ->withErrors([
                'name' => 'The name or password is incorrect.',
            ])
            ->onlyInput('name');
    }

    /**
     * Log out the current user.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
