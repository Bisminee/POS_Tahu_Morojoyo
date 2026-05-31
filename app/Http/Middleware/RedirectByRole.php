<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectByRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) return $next($request);

        $user = Auth::user();

        if ($user->role === 'kasir') {
            // Halaman absensi selalu boleh diakses
            if ($request->is('absensi*')) {
                return $next($request);
            }

            // Cek attendance aktif langsung by user_id, tidak perlu lewat shift
            $sudahAbsen = Attendance::where('user_id', $user->id)
                ->whereNotNull('jam_masuk')
                ->whereNull('jam_keluar')
                ->whereDate('jam_masuk', today())
                ->exists();

            if (!$sudahAbsen) {
                return redirect('/absensi');
            }

            // Sudah absen tapi bukan di halaman cashier → paksa ke POS
            if (!$request->is('cashier*')) {
                return redirect('/cashier/pos');
            }
        }

        return $next($request);
    }
}