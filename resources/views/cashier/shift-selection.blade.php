@props(['title' => 'Absensi Masuk'])

<x-layouts.app :title="$title">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red:        '#C0392B',
                            'red-dk':   '#96281B',
                            'red-lt':   '#E74C3C',
                            cream:      '#FAF6EF',
                            'cream-dk': '#F0E9DC',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: #FAF6EF; }</style>

    <div class="min-h-screen bg-brand-cream flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">

            {{-- Brand --}}
            <div class="text-center mb-5">
                <p class="text-xs font-bold tracking-[0.2em] text-brand-red/40 uppercase">· Tahu Bakso Morojoyo ·</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl shadow-brand-red/10 overflow-hidden border border-brand-cream-dk">

                @if (!$shift)
                    {{-- ── PILIH SHIFT ── --}}

                    {{-- Red Header --}}
                    <div class="bg-gradient-to-br from-brand-red to-brand-red-dk px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">🕐</div>
                            <div>
                                <h1 class="text-lg font-extrabold tracking-widest uppercase text-white">Pilih Shift Kasir</h1>
                                <p class="text-xs text-red-200 mt-0.5">Pilih karyawan yang sedang bertugas hari ini</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">

                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 text-brand-red text-sm font-semibold px-4 py-3 rounded-xl">
                                @foreach ($errors->all() as $error)
                                    <div>⚠️ {{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('cashier.select-shift.submit') }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                                    Nama Kasir Shift
                                </label>
                                <select name="shift_id" required
                                        class="w-full border border-brand-cream-dk rounded-xl px-4 py-2.5 text-sm bg-brand-cream focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition appearance-none">
                                    <option value="">Pilih kasir...</option>
                                    @foreach ($todayShifts as $s)
                                        <option value="{{ $s->id }}">
                                            {{ $s->karyawan?->nama ?? 'Kasir' }}
                                            — sesi {{ ucfirst($s->sesi) }}
                                            ({{ substr($s->jam_mulai, 0, 5) }} - {{ substr($s->jam_selesai, 0, 5) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                    class="w-full py-3.5 rounded-xl bg-brand-red hover:bg-brand-red-dk text-white font-extrabold text-sm tracking-[0.1em] uppercase transition-colors">
                                Pilih &amp; Lanjut ke Absensi →
                            </button>
                        </form>

                    </div>

                @else
                    {{-- ── VERIFIKASI WAJAH ── --}}

                    {{-- Red Header --}}
                    <div class="bg-gradient-to-br from-brand-red to-brand-red-dk px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">🪪</div>
                            <div>
                                <h1 class="text-lg font-extrabold tracking-widest uppercase text-white">Verifikasi Wajah</h1>
                                <p class="text-xs text-red-200 mt-0.5">Absensi masuk — {{ $shift->cabang->namaCabang }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">

                        {{-- Kasir Info --}}
                        <div class="flex items-center gap-3 bg-brand-cream border border-brand-cream-dk rounded-xl px-4 py-3">
                            <div class="w-9 h-9 bg-brand-red/10 rounded-lg flex items-center justify-center text-lg">👤</div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Kasir Bertugas</p>
                                <p class="text-sm font-bold text-gray-800">{{ $shift->karyawan->nama }}</p>
                            </div>
                        </div>

                        {{-- Camera --}}
                        <div class="relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3]">
                            <video id="video" autoplay playsinline
                                   class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>

                            {{-- Face Guide --}}
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div id="cam-guide"
                                     class="w-40 h-52 rounded-full border-[2.5px] border-white/40 transition-colors duration-300"
                                     style="box-shadow: 0 0 0 9999px rgba(0,0,0,0.4)"></div>
                            </div>

                            {{-- Status Pill --}}
                            <div id="cam-badge"
                                 class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 text-white text-[11px] font-medium px-3 py-1 rounded-full backdrop-blur-sm whitespace-nowrap">
                                Kamera siap
                            </div>
                        </div>

                        {{-- Status Box --}}
                        <div id="status-box" class="hidden rounded-xl px-4 py-3 text-sm font-semibold text-center border"></div>

                        {{-- Verify Button --}}
                        <button id="startVerification"
                                class="w-full py-3.5 rounded-xl bg-brand-red hover:bg-brand-red-dk text-white font-extrabold text-sm tracking-[0.1em] uppercase transition-colors flex items-center justify-center gap-2">
                            Mulai Verifikasi Wajah
                        </button>

                        {{-- Ganti Kasir --}}
                        <a href="{{ route('cashier.select-shift') }}"
                           class="flex items-center justify-center text-xs font-semibold text-gray-400 hover:text-brand-red transition-colors pt-1">
                            ← Ganti Kasir
                        </a>

                    </div>
                @endif

            </div>
        </div>
    </div>

</x-layouts.app>