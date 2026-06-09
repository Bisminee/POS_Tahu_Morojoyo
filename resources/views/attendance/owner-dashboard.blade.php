<x-layouts.app title="Rekap Absensi Karyawan">
    <style>
        body {
            background: #f4f5f7;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .page {
            padding: 32px;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 16px 50px rgba(15, 23, 42, .10);
            border: 1px solid #e5e7eb;
        }

        h1 {
            margin: 0 0 20px;
            font-size: 32px;
            font-weight: 800;
            color: #071331;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: end;
        }

        .filter-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .input {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 14px;
            background: #f8fafc;
        }

        .btn {
            border: none;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-success {
            background: #059669;
            color: white;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 16px;
            margin-top: 16px;
        }

        th {
            text-align: left;
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #111827;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
        }

        .badge-running {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-done {
            background: #dcfce7;
            color: #166534;
        }

        .empty {
            text-align: center;
            color: #64748b;
            padding: 24px;
        }
    </style>

    <div class="page">
        <div class="card">
            <h1>Rekap Absensi Karyawan</h1>

            <div class="top-actions">
                <form method="GET" action="{{ route('attendance.owner') }}" class="filter-form">
                    <div class="form-group">
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input
                            type="date"
                            id="tanggal_mulai"
                            name="tanggal_mulai"
                            class="input"
                            value="{{ $tanggalMulai }}">
                    </div>

                    <div class="form-group">
                        <label for="tanggal_selesai">Tanggal Selesai</label>
                        <input
                            type="date"
                            id="tanggal_selesai"
                            name="tanggal_selesai"
                            class="input"
                            value="{{ $tanggalSelesai }}">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Filter
                    </button>
                </form>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('owner.karyawan.list') }}" class="btn btn-secondary">
                        Face ID Karyawan
                    </a>

                    <a href="{{ route('attendance.export', [
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_selesai' => $tanggalSelesai
                    ]) }}" class="btn btn-success">
                        Unduh Sheet
                    </a>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Karyawan</th>
                        <th>Cabang Absensi</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Akun Kasir</th>
                        <th>Confidence Masuk</th>
                        <th>Confidence Pulang</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td>
                                {{ $attendance->tanggal?->format('d-m-Y') ?? '-' }}
                            </td>

                            <td>
                                {{ $attendance->karyawan?->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $attendance->cabang?->namaCabang ?? '-' }}
                            </td>

                            <td>
                                {{ $attendance->jam_masuk?->format('H:i:s') ?? '-' }}
                            </td>

                            <td>
                                {{ $attendance->jam_pulang?->format('H:i:s') ?? '-' }}
                            </td>

                            <td>
                                @if ($attendance->status === 'selesai')
                                    <span class="badge badge-done">Selesai</span>
                                @else
                                    <span class="badge badge-running">Sedang Shift</span>
                                @endif
                            </td>

                            <td>
                                {{ $attendance->user?->email ?? '-' }}
                            </td>

                            <td>
                                {{ $attendance->face_confidence_masuk ?? '-' }}
                            </td>

                            <td>
                                {{ $attendance->face_confidence_pulang ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty">
                                Belum ada data absensi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>