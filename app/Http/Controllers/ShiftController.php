<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /**
     * Halaman manajemen shift
     */
    public function index(Request $request)
    {
        $this->authorizeOwner();

        $tanggal = $request->input('tanggal', today()->format('Y-m-d'));

        $karyawan = Karyawan::all();

        $shifts = Shift::with(['karyawan', 'cabang'])
            ->where('tanggal', $tanggal)
            ->orderBy('jam_mulai')
            ->get();

        return view('shifts.index', compact('karyawan', 'shifts', 'tanggal'));
    }

    /**
     * Simpan shift baru
     */
    public function store(Request $request)
    {
        $this->authorizeOwner();

        $data = $request->validate([
            'karyawan_id'      => 'required|exists:karyawans,idKaryawan',
            'cabang_id'        => 'required|exists:cabangs,idCabang',
            'sesi'             => 'required|in:siang,sore',
            'tanggal'          => 'required|date',
            'jam_mulai'        => 'required|date_format:H:i',
            'jam_selesai'      => 'required|date_format:H:i|after:jam_mulai',
            'toleransi_menit'  => 'nullable|integer|min:0|max:60',
        ]);

        $data['toleransi_menit'] = $data['toleransi_menit'] ?? 15;

        // Cek duplikat shift sesi yang sama pada tanggal yang sama
        $exists = Shift::where('karyawan_id', $data['karyawan_id'])
            ->where('tanggal', $data['tanggal'])
            ->where('sesi', $data['sesi'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'sesi' => 'Karyawan sudah punya shift di sesi ini.',
            ]);
        }

        Shift::create($data);

        return back()->with('success', 'Shift berhasil ditambahkan.');
    }

    /**
     * Hapus shift
     */
    public function destroy(Shift $shift)
    {
        $this->authorizeOwner();

        $shift->delete();

        return back()->with('success', 'Shift berhasil dihapus.');
    }

    /**
     * Tutup shift secara manual oleh owner
     * jam_keluar dicatat saat owner menutup shift, bukan otomatis
     */
    public function close(Shift $shift)
    {
        $this->authorizeOwner();

        if ($shift->jam_keluar) {
            return back()->withErrors(['shift' => 'Shift sudah ditutup sebelumnya.']);
        }

        $shift->update([
            'jam_keluar' => now()->format('H:i'),
        ]);

        return back()->with('success', 'Shift berhasil ditutup.');
    }

    /**
     * Authorization owner
     */
    private function authorizeOwner(): void
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'owner') {
            abort(403);
        }
    }
}