<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN ABSENSI KASIR
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'kasir') {
            return redirect('/admin');
        }

        $activeAttendance = Attendance::with('karyawan')
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->latest()
            ->first();

        if ($activeAttendance) {
            session([
                'active_attendance_id' => $activeAttendance->id,
                'active_karyawan_id' => $activeAttendance->karyawan_id,
                'active_karyawan_name' => $activeAttendance->karyawan?->nama,
            ]);
        }

        // Semua karyawan aktif ditampilkan, tidak difilter cabang.
        $karyawans = Karyawan::query()
            ->where('is_active', 1)
            ->orderBy('nama')
            ->get();

        return view('attendance.index', compact('karyawans', 'activeAttendance'));
    }

    /*
    |--------------------------------------------------------------------------
    | ABSEN MASUK
    |--------------------------------------------------------------------------
    */
    public function clockIn(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'kasir') {
            abort(403, 'Hanya kasir yang dapat melakukan absensi.');
        }

        $request->validate([
            'karyawan_id' => ['required', 'exists:karyawans,idKaryawan'],
            'face_descriptor' => ['required', 'string'],
            'foto_base64' => ['required', 'string'],
        ]);

        $karyawan = Karyawan::query()
            ->where('idKaryawan', $request->karyawan_id)
            ->where('is_active', 1)
            ->firstOrFail();

        if (!$this->hasValidFaceDescriptor($karyawan->face_descriptor)) {
            return back()->withErrors([
                'face_descriptor' => "Face ID {$karyawan->nama} belum terdaftar. Silakan daftarkan Face ID terlebih dahulu di halaman owner.",
            ]);
        }

        $verification = $this->verifyFaceDescriptor(
            $request->input('face_descriptor'),
            $karyawan->face_descriptor
        );

        if (!$verification['verified']) {
            return back()->withErrors([
                'face_descriptor' => 'Wajah tidak cocok dengan Face ID ' . $karyawan->nama .
                    '. Distance: ' . $verification['distance'] .
                    ', Confidence: ' . $verification['confidence'] . '%. Silakan scan ulang.',
            ]);
        }

        $activeAttendance = Attendance::with('karyawan')
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->latest()
            ->first();

        if ($activeAttendance) {
            return back()->withErrors([
                'attendance' => 'Masih ada shift aktif atas nama ' . ($activeAttendance->karyawan?->nama ?? '-') . '. Selesaikan shift terlebih dahulu.',
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
            ->route('cashier.pos')
            ->with('success', "Absensi masuk berhasil. Selamat bekerja, {$karyawan->nama}!");
    }

    /*
    |--------------------------------------------------------------------------
    | ABSEN PULANG
    |--------------------------------------------------------------------------
    */
    public function clockOut(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'kasir') {
            abort(403, 'Hanya kasir yang dapat melakukan absensi.');
        }

        $request->validate([
            'face_descriptor' => ['required', 'string'],
            'foto_base64' => ['required', 'string'],
        ]);

        $attendanceId = session('active_attendance_id');

        $attendance = Attendance::with('karyawan')
            ->when($attendanceId, function ($query) use ($attendanceId) {
                $query->where('id', $attendanceId);
            })
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->latest()
            ->first();

        if (!$attendance) {
            session()->forget([
                'active_attendance_id',
                'active_karyawan_id',
                'active_karyawan_name',
            ]);

            return redirect()
                ->route('attendance.index')
                ->withErrors([
                    'attendance' => 'Tidak ada shift aktif yang perlu diselesaikan.',
                ]);
        }

        $karyawan = $attendance->karyawan;

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
                'face_descriptor' => 'Wajah tidak cocok. Absen pulang ditolak. Confidence: '
                    . $verification['confidence'] . '%. Silakan scan ulang.',
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

        session()->forget([
            'active_attendance_id',
            'active_karyawan_id',
            'active_karyawan_name',
        ]);

        return redirect()
            ->route('attendance.index')
            ->with('success', "Shift {$karyawan->nama} selesai. Jam pulang berhasil dicatat.");
    }

    /*
    |--------------------------------------------------------------------------
    | REKAP ABSENSI OWNER
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | EXPORT REKAP ABSENSI
    |--------------------------------------------------------------------------
    */
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

            // UTF-8 BOM agar Excel Indonesia tidak rusak encoding-nya.
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

    /*
    |--------------------------------------------------------------------------
    | DAFTAR FACE ID KARYAWAN OWNER
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | SIMPAN / UPDATE FACE ID KARYAWAN
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | HELPER: CEK FACE DESCRIPTOR VALID
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | HELPER: VERIFIKASI WAJAH
    |--------------------------------------------------------------------------
    */
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

        // Lebih kecil = lebih ketat.
        $threshold = 0.70;

        $confidence = max(0, round((1 - ($distance / $threshold)) * 100, 2));

        return [
            'verified' => $distance <= $threshold,
            'distance' => round($distance, 4),
            'confidence' => $confidence,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: SIMPAN FOTO BASE64
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | HELPER: OTORISASI OWNER
    |--------------------------------------------------------------------------
    */
    private function authorizeOwner(): void
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'owner') {
            abort(403, 'Hanya owner yang dapat mengakses halaman ini.');
        }
    }
}
