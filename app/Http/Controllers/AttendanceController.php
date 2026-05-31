<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Karyawan;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Halaman absensi sebelum masuk POS
     */
    public function index()
    {
        $user = Auth::user();

        $selectedShiftId = session('selected_shift_id');
        $shift = null;

        // Ambil cabang login
        $cabangId = $user->cabang_id;

        // Ambil shift hari ini sesuai cabang
        $todayShifts = Shift::with([
            'karyawan',
            'cabang',
        ])
            ->where('cabang_id', $cabangId)
            ->whereDate('tanggal', now()->toDateString())
            ->get()
            ->filter(fn($s) => $this->isShiftActive($s));

        // Kalau sudah pilih shift
        if ($selectedShiftId) {
            $shift = Shift::with([
                'karyawan',
                'cabang',
            ])->find($selectedShiftId);

            if (!$shift) {
                session()->forget('selected_shift_id');
                $shift = null;
            }
        }

        $cabangName = $shift?->cabang?->namaCabang;

        return view('attendance.index', [
            'shift' => $shift,
            'todayShifts' => $todayShifts,
            'cabangName' => $cabangName,
        ]);
    }

    /**
     * Pilih shift dari form dropdown
     */
    public function selectShift(Request $request)
    {
        $request->validate(['shift_id' => 'required|exists:shifts,id']);

        $shift = Shift::with(['karyawan', 'cabang'])->find($request->shift_id);

        if (!$shift) {
            return redirect()->route('attendance.index')
                ->withErrors(['shift_id' => 'Shift tidak ditemukan']);
        }

        session(['selected_shift_id' => $shift->id]);
        session()->save();

        return redirect()->route('attendance.index')
            ->with('success', 'Shift dipilih. Silakan lanjutkan verifikasi wajah.');
    }

    /**
     * Clock in / absen masuk
     */
    public function clockIn(Request $request)
    {
        $shift = Shift::with('karyawan')
            ->find(session('selected_shift_id'));

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Shift tidak ditemukan.'
            ], 404);
        }

        // Cek apakah sudah ada attendance aktif untuk shift ini
        $existingAttendance = Attendance::where('shift_id', $shift->id)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->first();

        // Jika sudah absen dan masih aktif, langsung redirect ke POS
        if ($existingAttendance) {
            return response()->json([
                'success' => true,
                'message' => 'Sudah diabsen. Lanjut ke POS.',
                'status_masuk' => $existingAttendance->status_masuk,
                'redirect' => route('cashier.pos'),
            ]);
        }

        $karyawan = $shift->karyawan;

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.'
            ], 404);
        }

        // Face descriptor referensi
        $storedDescriptor = $karyawan->face_descriptor;

        if (!$storedDescriptor) {
            return response()->json([
                'success' => false,
                'message' => 'Face ID belum terdaftar.'
            ], 422);
        }

        // Descriptor dari kamera
        $incomingDescriptor = $request->input('face_descriptor');

        if (!$incomingDescriptor) {
            return response()->json([
                'success' => false,
                'message' => 'Descriptor wajah tidak ditemukan.'
            ], 422);
        }

        // Hitung similarity
        $confidence = $this->cosineSimilarity(
            $incomingDescriptor,
            $storedDescriptor
        );

        // Threshold
        if ($confidence < 0.6) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak cocok.',
                'confidence' => round($confidence, 3),
            ], 422);
        }

        // Simpan foto absen
        $fotoPath = null;

        if ($request->input('foto_base64')) {
            $fotoPath = $this->saveBase64Photo(
                $request->input('foto_base64'),
                $karyawan->idKaryawan
            );
        }

        // Hitung status masuk
        $now = Carbon::now();

        [
            'status' => $status,
            'menit' => $telatMenit
        ] = $shift->hitungStatusMasuk($now);

        // Simpan attendance
        Attendance::create([
            'shift_id' => $shift->id,
            'user_id' => Auth::id(),
            'jam_masuk' => $now,
            'status_masuk' => $status,
            'telat_menit' => $telatMenit,
            'foto_absen' => $fotoPath,
            'face_confidence' => $confidence,
        ]);

        // Ensure session is persisted
        session()->save();

        $message = $status === 'telat'
            ? "Absen berhasil. Telat {$telatMenit} menit."
            : "Absen berhasil. Selamat bekerja!";

        return response()->json([
            'success' => true,
            'message' => $message,
            'status_masuk' => $status,
            'redirect' => route('attendance.verified'),
        ]);
    }

    /**
     * After successful face verification, redirect cashier to POS.
     */
    public function afterVerification()
    {
        $shift = Shift::find(session('selected_shift_id'));

        if (!$shift) {
            return redirect()->route('attendance.index')
                ->withErrors(['shift' => 'Shift belum dipilih. Silakan pilih shift terlebih dahulu.']);
        }

        $attendance = Attendance::where('shift_id', $shift->id)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->whereDate('created_at', today())
            ->first();

        if (!$attendance) {
            return redirect()->route('attendance.index')
                ->withErrors(['attendance' => 'Verifikasi wajah belum selesai. Silakan ulangi absensi.']);
        }

        return redirect()->route('cashier.pos');
    }

    public function verified(Request $request)
    {
        Log::info('✅ VERIFIED ROUTE HIT - Redirecting to cashier.pos');
        return redirect()->route('cashier.pos');
    }

    /**
     * Clock out / absen keluar
     */
    public function clockOut(Request $request)
    {
        $shift = Shift::find(session('selected_shift_id'));

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Shift tidak ditemukan.'
            ], 404);
        }

        $attendance = Attendance::where('shift_id', $shift->id)
            ->whereNull('jam_keluar')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada sesi aktif.'
            ], 404);
        }

        $attendance->update([
            'jam_keluar' => now(),
            'jenis_keluar' => $request->input('jenis_keluar', 'manual'),
            'catatan' => $request->input('catatan'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen keluar berhasil.',
        ]);
    }

    /**
     * Simpan face ID karyawan
     */
    public function saveFaceDataForKaryawan(
        Request $request,
        Karyawan $karyawan
    ) {
        $this->authorizeOwner();

        $request->validate([
            'face_descriptor' => 'required|array|min:128',
            'foto_base64' => 'required|string',
        ]);

        $fotoPath = $this->saveBase64Photo(
            $request->input('foto_base64'),
            $karyawan->idKaryawan,
            'ref'
        );

        $karyawan->update([
            'face_photo' => $fotoPath,
            'face_descriptor' => $request->input('face_descriptor'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Face ID berhasil disimpan.',
        ]);
    }

    /**
     * Dashboard owner
     */
    public function ownerDashboard(Request $request)
    {
        $this->authorizeOwner();

        $tanggal = $request->input(
            'tanggal',
            today()->format('Y-m-d')
        );

        $shifts = Shift::with([
            'karyawan',
            'attendances.karyawan'
        ])
            ->where('tanggal', $tanggal)
            ->orderBy('jam_mulai')
            ->get();

        $telat = Attendance::with([
            'karyawan',
            'shift'
        ])
            ->whereDate('jam_masuk', $tanggal)
            ->where('status_masuk', 'telat')
            ->orderByDesc('telat_menit')
            ->get();

        $tidakHadir = Shift::with('karyawan')
            ->where('tanggal', $tanggal)
            ->whereDoesntHave('attendances', function ($q) {
                $q->whereNotNull('jam_masuk');
            })
            ->get();

        return view('attendance.owner-dashboard', compact(
            'shifts',
            'telat',
            'tidakHadir',
            'tanggal'
        ));
    }

    /**
     * Halaman save face
     */
    public function showSaveFace()
    {
        return view('attendance.save-face');
    }

    /**
     * Similarity wajah
     */
    private function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b)) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $val) {
            $dot += $val * $b[$i];
            $normA += $val * $val;
            $normB += $b[$i] * $b[$i];
        }

        if ($normA == 0 || $normB == 0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Shift aktif
     */
    private function isShiftActive(Shift $shift): bool
    {
        $now = Carbon::now();

        $start = Carbon::parse(
            $shift->tanggal->format('Y-m-d') . ' ' . $shift->jam_mulai
        );

        $end = Carbon::parse(
            $shift->tanggal->format('Y-m-d') . ' ' . $shift->jam_selesai
        );

        return $now->between($start, $end);
    }


    /**
     * Simpan foto base64
     */
    private function saveBase64Photo(
        string $base64,
        int $karyawanId,
        string $prefix = 'absen'
    ): string {

        $base64 = preg_replace(
            '/^data:image\/\w+;base64,/',
            '',
            $base64
        );

        $binary = base64_decode($base64);

        $path = "attendance/{$prefix}_{$karyawanId}_" .
            now()->format('Ymd_His') .
            '.jpg';

        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    /**
     * Authorization owner
     */
    private function authorizeOwner(): void
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'owner') {
            abort(403, 'Hanya owner yang bisa mengakses.');
        }
    }
}
