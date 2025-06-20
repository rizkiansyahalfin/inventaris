<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Providers\RouteServiceProvider;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = auth()->user();
        
        if ($user->status !== 'active') {
            auth()->logout();
            
            if ($user->status === 'inactive') {
                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif. Silakan hubungi administrator untuk mengaktifkan akun Anda.',
                ])->withInput($request->except('password'));
            }
            
            if ($user->status === 'suspended') {
                return back()->withErrors([
                    'email' => 'Akun Anda ditangguhkan. Silakan hubungi administrator untuk informasi lebih lanjut.',
                ])->withInput($request->except('password'));
            }
        }

        $request->session()->regenerate();
        \App\Models\ActivityLog::log('login', 'auth', 'User login: ' . $user->name . ' (ID: ' . $user->id . ')');

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        if ($user) {
            \App\Models\ActivityLog::log('logout', 'auth', 'User logout: ' . $user->name . ' (ID: ' . $user->id . ')');
        }
        return redirect()->route('login');
    }
}
