<?php

namespace App\Http\Middleware;

use App\Models\Attendance;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckAttendance
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Bukan kasir → skip middleware
        if (!$user || $user->role !== 'kasir') {
            return $next($request);
        }

        // Ambil attendance aktif hari ini berdasarkan user_id
        $attendanceRecord = Attendance::whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        // Sync session dari attendance yang ada di DB
        if ($attendanceRecord) {
            $currentShiftInSession = session('selected_shift_id');
            if (!$currentShiftInSession || $currentShiftInSession != $attendanceRecord->shift_id) {
                session(['selected_shift_id' => $attendanceRecord->shift_id]);
                session()->save();
                Log::debug('Session synced from DB', [
                    'shift_id' => $attendanceRecord->shift_id,
                ]);
            }
        }

        $shiftId     = session('selected_shift_id');
        $hasAttendance = (bool) $attendanceRecord;

        Log::debug('CheckAttendance Debug', [
            'user_id'           => $user->id,
            'selected_shift_id' => $shiftId,
            'has_attendance'    => $hasAttendance,
            'route'             => $request->route()?->getName(),
            'today'             => Carbon::today()->toDateString(),
        ]);

        // ── ROUTE POS SELALU DIIZINKAN (absen tidak wajib) ──────────
        // Kasir boleh akses POS meski belum absen
        if ($request->routeIs('cashier.pos') || $request->routeIs('cashier.pos.checkout') || $request->routeIs('cashier.sync-sheets')) {
            return $next($request);
        }

        // ── ROUTE ABSENSI / PILIH SHIFT SELALU DIIZINKAN ────────────
        $allowedRoutes = [
            'attendance.*',
            'cashier.select-shift',
            'cashier.select-shift.submit',
        ];

        $isAllowedRoute = collect($allowedRoutes)
            ->contains(fn ($pattern) => $request->routeIs($pattern));

        if ($isAllowedRoute) {
            return $next($request);
        }

        // ── SUDAH ABSEN: jangan redirect balik ke absensi ────────────
        if ($hasAttendance) {
            return $next($request);
        }

        // ── BELUM ABSEN & bukan route yang diizinkan ─────────────────
        // Untuk route selain POS dan absensi, redirect ke absensi
        Log::info('Redirect to attendance', [
            'reason'   => 'no active attendance',
            'shift_id' => $shiftId,
        ]);

        return redirect()->route('attendance.index');
    }
}