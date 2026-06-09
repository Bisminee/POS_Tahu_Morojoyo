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

        if (!$user || $user->role !== 'kasir') {
            return $next($request);
        }

        $activeAttendances = Attendance::with('karyawan')
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->get();

        $validAttendances = $activeAttendances->filter(function ($attendance) {
            return $this->isValidFaceAttendance($attendance);
        });

        if ($validAttendances->count() > 0) {
            $firstAttendance = $validAttendances->first();

            session([
                'active_attendance_id' => $firstAttendance->id,
                'active_karyawan_id' => $firstAttendance->karyawan_id,
                'active_karyawan_name' => $firstAttendance->karyawan?->nama,
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

        if (!$this->hasValidFaceDescriptor($attendance->karyawan->face_descriptor)) {
            return false;
        }

        if (!$attendance->foto_masuk) {
            return false;
        }

        if ($attendance->face_confidence_masuk === null) {
            return false;
        }

        if (floatval($attendance->face_confidence_masuk) <= 0) {
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