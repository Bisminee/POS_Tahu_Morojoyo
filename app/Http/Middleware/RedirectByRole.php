<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectByRole
{
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Route admin / Filament
        |--------------------------------------------------------------------------
        | Jangan ganggu route /admin, biarkan Filament yang menangani login admin.
        | Nanti pembatasan role owner/manager tetap dicek di User.php melalui canAccessPanel().
        */
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Jika belum login
        |--------------------------------------------------------------------------
        | User guest boleh lanjut ke halaman guest dan halaman login.
        */
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | 3. Role Kasir
        |--------------------------------------------------------------------------
        | Kasir hanya boleh mengakses halaman kasir, absensi, dan logout.
        */
        if ($user->role === 'kasir') {
            if (
                $request->is('cashier') ||
                $request->is('cashier/*') ||
                $request->is('absensi') ||
                $request->is('absensi/*') ||
                $request->is('logout')
            ) {
                return $next($request);
            }

            return redirect('/cashier/pos');
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Role Owner dan Manager
        |--------------------------------------------------------------------------
        | Owner/manager tidak boleh masuk halaman login kasir, cashier, atau absensi.
        | Kalau mereka membuka halaman tersebut, arahkan ke admin panel.
        */
        if (in_array($user->role, ['owner', 'manager'])) {
            if (
                $request->is('login') ||
                $request->is('cashier') ||
                $request->is('cashier/*') ||
                $request->is('absensi') ||
                $request->is('absensi/*')
            ) {
                return redirect('/admin');
            }

            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Role tidak dikenal
        |--------------------------------------------------------------------------
        | Kalau role tidak sesuai, logout agar tidak nyangkut session.
        */
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->withErrors([
            'email' => 'Role akun tidak dikenali. Silakan hubungi administrator.',
        ]);
    }
}