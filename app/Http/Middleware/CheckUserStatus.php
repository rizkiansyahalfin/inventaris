<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->status !== 'active') {
            $status = auth()->user()->status;
            auth()->logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            if ($status === 'inactive') {
                return redirect()->route('login')
                    ->with('error', 'Akun Anda tidak aktif. Silakan hubungi administrator untuk mengaktifkan akun Anda.');
            }
            
            if ($status === 'suspended') {
                return redirect()->route('login')
                    ->with('error', 'Akun Anda ditangguhkan. Silakan hubungi administrator untuk informasi lebih lanjut.');
            }
        }

        return $next($request);
    }
}
