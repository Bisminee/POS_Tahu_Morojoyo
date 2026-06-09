<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $selectedShiftId = session('selected_shift_id');
        $shift = null;
        $cabangId = $user->cabang_id;

        // ✅ FILTER PENTING: Hanya ambil shift yang punya karyawan dengan user_id = user login
        // Sebelumnya: WHERE cabang_id (ambil semua shift cabang, padahal banyak karyawan)
        // Sesudah: JOIN dengan karyawan & user untuk filter per user

        $todayShifts = Shift::with(['karyawan', 'cabang'])
            ->where('cabang_id', $cabangId)
            ->whereDate('tanggal', now()->toDateString())
            ->whereHas('karyawan') // pastikan shift punya karyawan
            ->get()
            ->filter(fn($s) => $this->isShiftActive($s));

        // Kalau sudah pilih shift
        if ($selectedShiftId) {
            $shift = Shift::with(['karyawan', 'cabang'])->find($selectedShiftId);

            if (!$shift) {
                session()->forget('selected_shift_id');
                $shift = null;
            } elseif (!$this->isShiftActive($shift)) {
                session()->forget('selected_shift_id');
                $shift = null;
            } elseif (!$shift->karyawan) {
                session()->forget('selected_shift_id');
                $shift = null;
            } elseif ($shift->cabang_id !== $user->cabang_id) {
                session()->forget('selected_shift_id');
                $shift = null;
            }
        }

        $activeAttendances = Attendance::with('karyawan')
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->orderBy('jam_masuk')
            ->get();

    public function resetAndGoToAbsensi()
    {
        session()->forget('selected_shift_id');
        session()->save();
        return redirect()->route('attendance.index');
    }

    public function selectShift(Request $request)
    {
        $request->validate(['shift_id' => 'required|exists:shifts,id']);

        $karyawans = Karyawan::query()
            ->where('is_active', 1)
            ->when(count($karyawanAktifIds), function ($query) use ($karyawanAktifIds) {
                $query->whereNotIn('idKaryawan', $karyawanAktifIds);
            })
            ->orderBy('nama')
            ->get();

        if (!$shift || !$shift->karyawan) {
            return redirect()->route('attendance.index')
                ->withErrors(['shift_id' => 'Shift tidak valid.']);
        }

        if ($shift->cabang_id !== auth()->user()->cabang_id) {
            return redirect()->route('attendance.index')
                ->withErrors(['shift_id' => 'Shift bukan milik cabang Anda.']);
        }

        session(['selected_shift_id' => $shift->id]);
        session()->save(); // ← pastikan ada ini

        return redirect()->route('attendance.index');
    }


    public function clockIn(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'kasir') {
            abort(403, 'Hanya kasir yang dapat melakukan absensi.');
        }

        // Ambil shift aktif dari session
        $shiftId = session('selected_shift_id');
        $shift   = Shift::with('karyawan')->find($shiftId);

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Shift tidak ditemukan. Pilih shift dulu.'
            ]);
        }

        $karyawan = $shift->karyawan;

        // ✅ VALIDASI KETAT:
        // Pastikan karyawan di shift = user yang sedang login
        if (!$karyawan) {
            session()->forget('selected_shift_id');
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.',
            ], 403);
        }

        if ($shift->cabang_id !== $user->cabang_id) {
            session()->forget('selected_shift_id');
            return response()->json([
                'success' => false,
                'message' => 'Shift ini bukan milik cabang Anda.',
            ], 403);
        }

        // ── VALIDASI FACE DESCRIPTOR ─────────────────────────────────────────
        if (!$karyawan->face_descriptor) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan belum mendaftarkan wajah.'
            ]);
        }

        $incomingDescriptor = $request->input('face_descriptor');
        $frontendDistance   = (float) $request->input('face_distance', 1.0);
        $THRESHOLD          = 0.40;

        if (!is_array($incomingDescriptor) || count($incomingDescriptor) !== 128) {
            return response()->json([
                'success' => false,
                'message' => 'Data wajah tidak valid.'
            ]);
        }

        $registeredRaw = is_string($karyawan->face_descriptor)
            ? json_decode($karyawan->face_descriptor, true)
            : (array) $karyawan->face_descriptor;

        if (!is_array($registeredRaw) || count($registeredRaw) !== 128) {
            return response()->json([
                'success' => false,
                'message' => 'Data wajah karyawan rusak. Hubungi owner.'
            ]);
        }

        $distance = $this->euclideanDistance(
            array_values($incomingDescriptor),
            array_values($registeredRaw)
        );

        Log::info('Face verification', [
            'user_id'           => $user->id,
            'karyawan_id'       => $karyawan->idKaryawan,
            'karyawan'          => $karyawan->nama,
            'distance_server'   => round($distance, 4),
            'distance_frontend' => round($frontendDistance, 4),
            'threshold'         => $THRESHOLD,
            'match'             => $distance <= $THRESHOLD,
        ]);

        if ($distance > $THRESHOLD) {
            return response()->json([
                'success' => false,
                'message' => "Wajah tidak cocok dengan {$karyawan->nama}. Gunakan wajah yang terdaftar.",
            ]);
        }

        $fotoMasuk = $this->saveBase64Photo(
            $request->input('foto_base64'),
            $karyawan->idKaryawan,
            'masuk'
        );

        $attendance = Attendance::create([
            'karyawan_id' => $karyawan->idKaryawan,
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id,
            'tanggal' => today()->toDateString(),
            'jam_masuk' => now(),
            'jam_pulang' => null,
            'foto_masuk' => $fotoMasuk,
            'face_confidence_masuk' => $verification['confidence'],
            'status' => 'sedang_shift',
        ]);

        session([
            'active_attendance_id' => $attendance->id,
            'active_karyawan_id' => $karyawan->idKaryawan,
            'active_karyawan_name' => $karyawan->nama,
        ]);

        return redirect()
            ->route('attendance.index')
            ->with('success', "Absensi masuk {$karyawan->nama} berhasil dicatat.");
    }

    public function clockOut(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'kasir') {
            abort(403, 'Hanya kasir yang dapat melakukan absensi.');
        }

        $request->validate([
            'attendance_id' => ['required', 'exists:attendances,id'],
            'face_descriptor' => ['required', 'string'],
            'foto_base64' => ['required', 'string'],
        ]);

        $attendance = Attendance::with('karyawan')
            ->where('id', $request->attendance_id)
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->first();

        if (!$attendance) {
            return redirect()
                ->route('attendance.index')
                ->withErrors([
                    'attendance' => 'Data shift aktif tidak ditemukan atau sudah selesai.',
                ]);
        }

        return redirect()->route('cashier.pos');
    }

    public function verified(Request $request)
    {
        Log::info('VERIFIED ROUTE HIT - Redirecting to cashier.pos');
        return redirect()->route('cashier.pos');
    }

        if (!$karyawan || !$this->hasValidFaceDescriptor($karyawan->face_descriptor)) {
            return back()->withErrors([
                'face_descriptor' => 'Face ID karyawan belum terdaftar atau datanya tidak valid.',
            ]);
        }

        $verification = $this->verifyFaceDescriptor(
            $request->input('face_descriptor'),
            $karyawan->face_descriptor
        );

        if (!$verification['verified']) {
            return back()->withErrors([
                'face_descriptor' => 'Wajah tidak cocok dengan Face ID '
                    . $karyawan->nama
                    . '. Distance: '
                    . $verification['distance']
                    . ', Confidence: '
                    . $verification['confidence']
                    . '%. Absen pulang ditolak.',
            ]);
        }

        $fotoPulang = $this->saveBase64Photo(
            $request->input('foto_base64'),
            $karyawan->idKaryawan,
            'pulang'
        );

        $attendance->update([
            'jam_pulang' => now(),
            'foto_pulang' => $fotoPulang,
            'face_confidence_pulang' => $verification['confidence'],
            'status' => 'selesai',
            'catatan' => $request->input('catatan'),
        ]);

        // 👇 TAMBAHAN: Clear session shift
        session()->forget('selected_shift_id');
        session()->save();

        return response()->json([
            'success' => true,
            'message' => 'Absen keluar berhasil.',
        ]);
    }

    public function ownerDashboard(Request $request)
    {
        $this->authorizeOwner();

        $tanggalMulai = $request->input('tanggal_mulai', today()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', today()->toDateString());

        $attendances = Attendance::with(['karyawan', 'cabang', 'user'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_masuk')
            ->get();

        return view('attendance.owner-dashboard', compact(
            'attendances',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    public function exportAbsensiCsv(Request $request)
    {
        $this->authorizeOwner();

        $tanggalMulai = $request->input('tanggal_mulai', today()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', today()->toDateString());

        $attendances = Attendance::with(['karyawan', 'cabang', 'user'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal')
            ->orderBy('jam_masuk')
            ->get();

        $filename = "rekap_absensi_{$tanggalMulai}_sampai_{$tanggalSelesai}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Tanggal',
                'Nama Karyawan',
                'Cabang Absensi',
                'Jam Masuk',
                'Jam Pulang',
                'Status',
                'Akun Kasir',
                'Confidence Masuk',
                'Confidence Pulang',
            ]);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->tanggal?->format('d-m-Y') ?? '-',
                    $attendance->karyawan?->nama ?? '-',
                    $attendance->cabang?->namaCabang ?? '-',
                    $attendance->jam_masuk?->format('H:i:s') ?? '-',
                    $attendance->jam_pulang?->format('H:i:s') ?? '-',
                    $attendance->status ?? '-',
                    $attendance->user?->email ?? '-',
                    $attendance->face_confidence_masuk ?? '-',
                    $attendance->face_confidence_pulang ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function karyawanList(Request $request)
    {
        $this->authorizeOwner();

        $search = $request->input('search');

        $karyawans = Karyawan::query()
            ->when($search, function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->get();

        return view('owner.karyawan-list', compact('karyawans', 'search'));
    }

    public function saveFaceDataForKaryawan(Request $request, Karyawan $karyawan)
    {
        $this->authorizeOwner();

        $request->validate([
            'face_descriptor' => ['required'],
            'foto_base64' => ['nullable', 'string'],
        ]);

        $descriptor = $request->input('face_descriptor');

        if (is_string($descriptor)) {
            $descriptorArray = json_decode($descriptor, true);
        } else {
            $descriptorArray = $descriptor;
        }

        if (!is_array($descriptorArray) || count($descriptorArray) < 100) {
            return back()->withErrors([
                'face_descriptor' => 'Data Face ID tidak valid. Silakan scan wajah ulang.',
            ]);
        }

        $fotoPath = $karyawan->face_photo;

        if ($request->filled('foto_base64')) {
            $fotoPath = $this->saveBase64Photo(
                $request->input('foto_base64'),
                $karyawan->idKaryawan,
                'face'
            );
        }

        $karyawan->update([
            'face_photo' => $fotoPath,
            'face_descriptor' => json_encode($descriptorArray),
        ]);

        return redirect()
            ->route('owner.karyawan.list')
            ->with('success', "Face ID {$karyawan->nama} berhasil disimpan.");
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

    private function verifyFaceDescriptor(string $inputDescriptor, string $storedDescriptor): array
    {
        $input = json_decode($inputDescriptor, true);
        $stored = json_decode($storedDescriptor, true);

        if (!is_array($input) || !is_array($stored)) {
            return [
                'verified' => false,
                'distance' => null,
                'confidence' => 0,
            ];
        }

        if (count($input) < 100 || count($stored) < 100) {
            return [
                'verified' => false,
                'distance' => null,
                'confidence' => 0,
            ];
        }

        if (count($input) !== count($stored)) {
            return [
                'verified' => false,
                'distance' => null,
                'confidence' => 0,
            ];
        }

        $sum = 0;

        foreach ($input as $index => $value) {
            $diff = floatval($value) - floatval($stored[$index]);
            $sum += $diff * $diff;
        }

        $distance = sqrt($sum);

        /*
         * 0.50 = cukup aman.
         * Jika wajah sama sering gagal, naikkan ke 0.55.
         * Jika wajah berbeda masih lolos, turunkan ke 0.45.
         */
        $threshold = 0.60;

        $confidence = max(0, round((1 - ($distance / $threshold)) * 100, 2));

        return [
            'verified' => $distance <= $threshold,
            'distance' => round($distance, 4),
            'confidence' => $confidence,
        ];
    }

    private function saveBase64Photo(string $base64, int $karyawanId, string $prefix = 'face'): string
    {
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);

        $binary = base64_decode($base64);

        if ($binary === false) {
            abort(422, 'Format foto tidak valid.');
        }

        $path = "face-id/{$prefix}_karyawan_{$karyawanId}_" . now()->format('Ymd_His') . '.jpg';

        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    private function authorizeOwner(): void
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'owner') {
            abort(403, 'Hanya owner yang dapat mengakses halaman ini.');
        }
    }
}