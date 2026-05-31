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

        $hasAttendance = (bool) $attendanceRecord;

        // Sync session dari attendance yang ada di DB
        // Ini penting agar session tidak hilang setelah redirect
        if ($attendanceRecord) {
            $currentShiftInSession = session('selected_shift_id');
            if (!$currentShiftInSession || $currentShiftInSession != $attendanceRecord->shift_id) {
                session(['selected_shift_id' => $attendanceRecord->shift_id]);
                session()->save();
                Log::debug('Session synced from DB', [
                    'shift_id' => $attendanceRecord->shift_id
                ]);
            }
        }

        // Ambil shift_id SETELAH session di-sync
        $shiftId = session('selected_shift_id');

        Log::debug('CheckAttendance Debug', [
            'user_id'           => $user->id,
            'selected_shift_id' => $shiftId,
            'has_attendance'    => $hasAttendance,
            'route'             => $request->route()?->getName(),
            'today'             => Carbon::today()->toDateString(),
        ]);

        // ── ROUTE YANG SELALU DIIZINKAN ──────────────────────────────
        $attendanceRoutes = [
            'attendance.*',
            'cashier.select-shift',
            'cashier.select-shift.submit',
        ];

        $isAttendanceRoute = collect($attendanceRoutes)
            ->contains(fn($pattern) => $request->routeIs($pattern));

        // ── SUDAH ABSEN ───────────────────────────────────────────────
        if ($hasAttendance) {
            // Jika mencoba akses halaman absensi/pilih shift → paksa ke POS
            if ($request->routeIs('attendance.index') || $request->routeIs('attendance.select-shift')) {
                Log::info('Redirect to POS (already has attendance)');
                return redirect()->route('cashier.pos');
            }

            // Lanjutkan ke route yang dituju (termasuk cashier.pos)
            Log::debug('Middleware Passed (has attendance)');
            return $next($request);
        }

        // ── BELUM ABSEN ───────────────────────────────────────────────
        if (!$isAttendanceRoute) {
            Log::info('Redirect to attendance', [
                'reason'   => 'no active attendance',
                'shift_id' => $shiftId,
            ]);
            return redirect()->route('attendance.index');
        }

        Log::debug('Middleware Passed (attendance route)');
        return $next($request);
    }
}