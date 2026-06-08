<?php

namespace App\Http\Middleware;

use App\Models\Attendance;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAttendance
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Kalau bukan kasir, biarkan lanjut.
        if (!$user || $user->role !== 'kasir') {
            return $next($request);
        }

        // Cek session aktif.
        $activeAttendanceId = session('active_attendance_id');

        if ($activeAttendanceId) {
            $attendance = Attendance::where('id', $activeAttendanceId)
                ->where('user_id', $user->id)
                ->whereDate('tanggal', today())
                ->whereNotNull('jam_masuk')
                ->whereNull('jam_pulang')
                ->first();

            if ($attendance) {
                return $next($request);
            }

            session()->forget([
                'active_attendance_id',
                'active_karyawan_id',
                'active_karyawan_name',
            ]);
        }

        // Kalau session hilang, cek dari database apakah masih ada shift aktif hari ini.
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->latest()
            ->first();

        if ($attendance) {
            session([
                'active_attendance_id' => $attendance->id,
                'active_karyawan_id' => $attendance->karyawan_id,
                'active_karyawan_name' => $attendance->karyawan?->nama,
            ]);

            return $next($request);
        }

        return redirect()
            ->route('attendance.index')
            ->with('error', 'Silakan lakukan absensi masuk sebelum membuka POS.');
    }
}