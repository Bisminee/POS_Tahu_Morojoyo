<x-layouts.app title="Rekap Absensi Karyawan">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red:       '#C0392B',
                            'red-dk':  '#96281B',
                            'red-lt':  '#E74C3C',
                            cream:     '#FAF6EF',
                            'cream-dk':'#F0E9DC',
                        }
                    },
                    fontFamily: {
                        jakarta: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }</style>

    <div class="min-h-screen bg-brand-cream px-4 py-8 md:px-8">

        <div class="text-center mb-6">
            <p class="text-xs font-bold tracking-[0.2em] text-brand-red/40 uppercase">· Tahu Bakso Morojoyo ·</p>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl shadow-brand-red/10 overflow-hidden border border-brand-cream-dk">

                {{-- Red Header --}}
                <div class="bg-brand-red to-brand-red-dk px-6 py-5">
                    <h1 class="text-xl font-extrabold tracking-widest uppercase text-white">Rekap Absensi Karyawan</h1>
                    <p class="text-sm text-red-200 mt-0.5">Pantau kehadiran seluruh karyawan per periode</p>
                </div>

                <div class="p-6">

                    {{-- Filter & Actions --}}
                    <div class="flex flex-wrap gap-3 justify-between items-end mb-6">
                        <form method="GET" action="{{ route('attendance.owner') }}" class="flex flex-wrap gap-3 items-end">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                                       class="border border-brand-cream-dk rounded-xl px-4 py-2.5 text-sm bg-brand-cream focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}"
                                       class="border border-brand-cream-dk rounded-xl px-4 py-2.5 text-sm bg-brand-cream focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition">
                            </div>
                            <button type="submit"
                                    class="bg-brand-red hover:bg-brand-red-dk text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
                                Filter
                            </button>
                        </form>

                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('owner.karyawan.list') }}"
                               class="bg-brand-cream-dk hover:bg-brand-cream-dk/70 text-gray-700 text-sm font-bold px-4 py-2.5 rounded-xl transition-colors border border-brand-cream-dk">
                                Face ID Karyawan
                            </a>
                            <a href="{{ route('attendance.export', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}"
                               class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                                Unduh Sheet
                            </a>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto rounded-xl border border-brand-cream-dk">
                        <table class="w-full border-collapse min-w-[900px]">
                            <thead>
                                <tr class="bg-brand-cream">
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Tanggal</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Nama Karyawan</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Cabang</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Jam Masuk</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Jam Pulang</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Status</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Akun Kasir</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Conf. Masuk</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Conf. Pulang</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-brand-cream-dk">
                                @forelse ($attendances as $attendance)
                                    <tr class="hover:bg-brand-cream/50 transition-colors">
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">
                                            {{ $attendance->tanggal?->format('d-m-Y') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">
                                            {{ $attendance->karyawan?->nama ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $attendance->cabang?->namaCabang ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $attendance->jam_masuk?->format('H:i:s') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $attendance->jam_pulang?->format('H:i:s') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($attendance->status === 'selesai')
                                                <span class="inline-flex items-center bg-green-50 text-green-800 border border-green-200 text-xs font-bold px-3 py-1.5 rounded-full">
                                                    ✓ Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold px-3 py-1.5 rounded-full">
                                                    ⏳ Sedang Shift
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $attendance->user?->email ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $attendance->face_confidence_masuk ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $attendance->face_confidence_pulang ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-gray-400 py-10 text-sm">
                                            Belum ada data absensi pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>