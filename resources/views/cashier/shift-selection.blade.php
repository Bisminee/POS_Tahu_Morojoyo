@props(['title' => 'Absensi Masuk'])

<x-layouts.app :title="$title">

    <div class="min-h-screen bg-[#FDF6EC] px-4 py-10 font-sans">

        <div class="mx-auto max-w-lg overflow-hidden rounded-[20px] bg-white shadow-lg">

            {{-- HEADER MERAH --}}
            <div class="bg-[#C0392B] px-8 py-7">

                <div class="flex justify-center mb-8">
                    @if(isset($identitas) && $identitas->logo)
                        <img src="{{ asset('storage/' . $identitas->logo) }}" alt="{{ $identitas->nama_brand ?? 'Logo' }}"class="h-20 w-auto">
                    @endif
                </div>

                <hr class="mb-5 border-white/20">

                @if (!$shift)
                    <h1 class="text-xl font-extrabold uppercase tracking-wide text-white">Pilih Shift Kasir</h1>
                    <p class="mt-1 text-sm text-white/70">Pilih karyawan yang sedang bertugas hari ini</p>
                @else
                    <h1 class="text-xl font-extrabold uppercase tracking-wide text-white">Verifikasi Wajah</h1>
                    <p class="mt-1 text-sm text-white/70">Kasir: {{ $shift->karyawan->nama }} · {{ $shift->cabang->namaCabang }}</p>
                @endif

            </div>

            {{-- BODY --}}
            <div class="px-8 py-7">

                @if (!$shift)

                    {{-- ERROR --}}
                    @if ($errors->any())
                        <div class="mb-5 rounded-xl bg-red-50 p-4 text-sm text-red-700 ring-1 ring-red-200">
                            <p class="font-semibold">Terjadi kesalahan:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- INFO BOX --}}
                    <div class="mb-6 rounded-xl border border-[#F5C58A] bg-[#FEF6EB] px-4 py-3 text-[13px] leading-relaxed text-[#7A4A1A]">
                        Gunakan akun kasir cabang untuk masuk.<br>
                        Contoh: <strong class="text-[#C0392B]">kasir.cabang1@gmail.com</strong> untuk Dinoyo &amp;
                        <strong class="text-[#C0392B]">kasir.cabang2@gmail.com</strong> untuk Suhat.
                    </div>

                    <form action="{{ route('cashier.select-shift.submit') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-[#4B4B4B]">
                                Nama Kasir Shift <span class="text-[#C0392B]">*</span>
                            </label>

                            <select name="shift_id" required
                                class="w-full appearance-none rounded-xl border-[1.5px] border-[#E0D5C8] bg-[#FAF7F3] px-4 py-3 text-sm text-gray-700 outline-none focus:border-[#C0392B]">
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
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#C0392B] px-4 py-4 text-[13px] font-extrabold uppercase tracking-widest text-white hover:bg-[#A93226] active:scale-[0.98]">
                            Pilih &amp; Lanjut ke Absensi
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>

                    </form>

                    <p class="mt-5 text-center text-[13px] text-gray-400">
                        Lupa password? Hubungi <strong class="cursor-pointer text-[#C0392B]">Administrator</strong>
                    </p>

                @else

                    {{-- KASIR INFO --}}
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#FAD7D0] text-lg font-black text-[#C0392B]">
                            {{ strtoupper(substr($shift->karyawan->nama, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ $shift->karyawan->nama }}</p>
                            <p class="text-[13px] text-gray-400">{{ $shift->cabang->namaCabang }} · Sesi {{ ucfirst($shift->sesi) }}</p>
                        </div>
                    </div>

                    <hr class="mb-5 border-[#F0EBE3]">

                    {{-- VIDEO --}}
                    <div>
                        <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-[#4B4B4B]">
                            Kamera Verifikasi
                        </label>
                        <video id="video" autoplay playsinline
                            class="w-full rounded-2xl border-2 border-dashed border-[#D9D0C4] bg-[#F0EBE3] aspect-video object-cover">
                        </video>
                    </div>

                    <button id="startVerification"
                        class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-[#1E8449] px-4 py-4 text-[13px] font-extrabold uppercase tracking-widest text-white hover:bg-[#176038] active:scale-[0.98]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Mulai Verifikasi Wajah
                    </button>

                    <p class="mt-5 text-center text-[13px]">
                        <a href="{{ route('cashier.select-shift') }}" class="font-medium text-[#C0392B]">← Ganti kasir</a>
                    </p>

                @endif

            </div>

        </div>

    </div>

</x-layouts.app>