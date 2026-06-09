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

        $activeAttendanceId = session('active_attendance_id');

        if ($activeAttendanceId) {
            $attendance = Attendance::with('karyawan')
                ->where('id', $activeAttendanceId)
                ->where('user_id', $user->id)
                ->whereDate('tanggal', today())
                ->whereNotNull('jam_masuk')
                ->whereNull('jam_pulang')
                ->first();

            if ($this->isValidFaceAttendance($attendance)) {
                session([
                    'active_attendance_id' => $attendance->id,
                    'active_karyawan_id' => $attendance->karyawan_id,
                    'active_karyawan_name' => $attendance->karyawan?->nama,
                ]);

                return $next($request);
            }

            session()->forget([
                'active_attendance_id',
                'active_karyawan_id',
                'active_karyawan_name',
            ]);
        }

        // Kalau session hilang, cek database.
        // Tetapi hanya attendance yang benar-benar dibuat lewat Face ID yang boleh lolos.
        $attendance = Attendance::with('karyawan')
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->latest()
            ->first();

        if ($this->isValidFaceAttendance($attendance)) {
            session([
                'active_attendance_id' => $attendance->id,
                'active_karyawan_id' => $attendance->karyawan_id,
                'active_karyawan_name' => $attendance->karyawan?->nama,
            ]);

            return $next($request);
        }

        session()->forget([
            'active_attendance_id',
            'active_karyawan_id',
            'active_karyawan_name',
        ]);

        return redirect()
            ->route('attendance.index')
            ->with('error', 'Silakan lakukan absensi masuk menggunakan Face ID terlebih dahulu sebelum membuka POS.');
    }

    private function isValidFaceAttendance(?Attendance $attendance): bool
    {
        if (!$attendance) {
            return false;
        }

        if (!$attendance->karyawan) {
            return false;
        }

        // Karyawan wajib punya Face ID terdaftar.
        if (!$this->hasValidFaceDescriptor($attendance->karyawan->face_descriptor)) {
            return false;
        }

        // Attendance wajib punya foto masuk hasil scan Face ID.
        if (!$attendance->foto_masuk) {
            return false;
        }

        // Attendance wajib punya confidence masuk.
        if ($attendance->face_confidence_masuk === null) {
            return false;
        }

        /*
         * Minimal confidence.
         * Kalau masih terlalu ketat, turunkan ke 30.
         * Kalau ingin lebih aman, naikkan ke 50.
         */
        if (floatval($attendance->face_confidence_masuk) < 30) {
            return false;
        }

        return true;
    }

    private function hasValidFaceDescriptor(?string $descriptor): bool
    {
        if ($descriptor === null) {
            return false;
        }

        $descriptor = trim($descriptor);

        if ($descriptor === '' || strtolower($descriptor) === 'null' || $descriptor === '[]') {
            return false;
        }

        $decoded = json_decode($descriptor, true);

        if (!is_array($decoded)) {
            return false;
        }

        if (count($decoded) < 100) {
            return false;
        }

        return true;
    }
}