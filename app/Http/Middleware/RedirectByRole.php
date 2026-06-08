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
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user->role === 'kasir') {
            // Halaman absensi selalu boleh diakses
            if ($request->is('absensi*')) {
                return $next($request);
            }

            // Kasir hanya boleh akses halaman cashier & absensi
            // Tidak perlu cek sudah absen atau belum untuk akses POS
            if (!$request->is('cashier*')) {
                return redirect('/cashier/pos');
            }
        }

        return $next($request);
    }
}
