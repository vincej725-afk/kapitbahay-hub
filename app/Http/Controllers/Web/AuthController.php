<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the staff/owner login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            return $user->role === 'owner'
                ? redirect()->route('owner.dashboard')
                : redirect()->route('staff.orders.index');
        }

        return view('auth.login');
    }

    /**
     * Handle session login for staff website.
     * Implements Q2(c) - Session-based login for web clients.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'owner') {
                return redirect()->intended(route('owner.dashboard'));
            }

            if ($user->role === 'staff') {
                return redirect()->intended(route('staff.orders.index'));
            }

            // Customer redirected to profile or home
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['This portal is restricted to staff and owner accounts. Customers use the mobile app.'],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Log out of the web portal.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out successfully.');
    }
}
