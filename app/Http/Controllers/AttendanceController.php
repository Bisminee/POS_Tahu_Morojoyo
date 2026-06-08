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
use Illuminate\Http\JsonResponse;

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

        $cabangName = $shift?->cabang?->namaCabang;

        return view('attendance.index', [
            'shift' => $shift,
            'todayShifts' => $todayShifts,
            'cabangName' => $cabangName,
        ]);
    }

    public function resetAndGoToAbsensi()
    {
        session()->forget('selected_shift_id');
        session()->save();
        return redirect()->route('attendance.index');
    }

    public function selectShift(Request $request)
    {
        $request->validate(['shift_id' => 'required|exists:shifts,id']);

        $shift = Shift::with(['karyawan', 'cabang'])->find($request->shift_id);

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
        $user = $request->user();

        if (!$user || $user->role !== 'kasir') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
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

        // ── CEK SUDAH ABSEN HARI INI ─────────────────────────────────────────
        $alreadyAbsen = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->exists();

        if ($alreadyAbsen) {
            return response()->json([
                'success'  => true,
                'message'  => 'Kamu sudah absen hari ini.',
                'redirect' => route('cashier.pos'),
            ]);
        }

        // ── CATAT ABSENSI ────────────────────────────────────────────────────
        $jamMasuk      = now();
        $jamMulaiShift = Carbon::parse($shift->jam_mulai);
        $toleransi     = (int) ($shift->toleransi_menit ?? 15);
        $statusMasuk   = $jamMasuk->gt($jamMulaiShift->copy()->addMinutes($toleransi))
            ? 'telat'
            : 'tepat_waktu';

        Attendance::create([
            'user_id'      => $user->id,
            'shift_id'     => $shift->id,
            'karyawan_id'  => $karyawan->idKaryawan,
            'jam_masuk'    => $jamMasuk,
            'status_masuk' => $statusMasuk,
            'foto_masuk'   => $request->input('foto_base64'),
        ]);

        $telat = $jamMasuk->diffInMinutes($jamMulaiShift);
        $msg   = $statusMasuk === 'telat'
            ? "Absen berhasil tapi telat {$telat} menit."
            : "Selamat bekerja, {$karyawan->nama}! Absen tercatat.";

        return response()->json([
            'success'      => true,
            'message'      => $msg,
            'status_masuk' => $statusMasuk,
            'redirect'     => route('cashier.pos'),
        ]);
    }

    /**
     * Hitung Euclidean distance antara dua descriptor (array float[128]).
     */
    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0.0;
        for ($i = 0; $i < 128; $i++) {
            $diff = ((float) ($a[$i] ?? 0.0)) - ((float) ($b[$i] ?? 0.0));
            $sum += $diff * $diff;
        }
        return sqrt($sum);
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
        Log::info('VERIFIED ROUTE HIT - Redirecting to cashier.pos');
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

        // 👇 TAMBAHAN: Clear session shift
        session()->forget('selected_shift_id');
        session()->save();

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

    public function karyawanList(Request $request)
    {
        if (auth()->user()->role !== 'owner') {
            abort(403);
        }

        $search = $request->input('search');

        $karyawans = \App\Models\Karyawan::with(['cabang', 'user'])
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('nama')
            ->get();

        return view('owner.karyawan-list', compact('karyawans'));
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
