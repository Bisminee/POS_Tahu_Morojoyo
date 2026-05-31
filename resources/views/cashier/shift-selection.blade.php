@props(['title' => 'Absensi Masuk'])

<x-layouts.app :title="$title">

    <div class="mx-auto max-w-2xl px-4 py-12">

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">

            {{-- JIKA BELUM PILIH SHIFT --}}
            @if (!$shift)

                <h1 class="text-3xl font-semibold text-slate-900">
                    Pilih Kasir Shift Hari Ini
                </h1>

                <p class="mt-2 text-slate-600">
                    Pilih karyawan yang sedang bertugas.
                </p>

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl bg-rose-50 p-4 text-rose-700 ring-1 ring-rose-200">

                        <p class="font-semibold">
                            Terjadi kesalahan:
                        </p>

                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>
                @endif

                <form action="{{ route('cashier.select-shift.submit') }}" method="POST" class="mt-8 space-y-6">

                    @csrf

                    <div>

                        <label class="block text-sm font-medium text-slate-700">
                            Nama Kasir Shift
                        </label>

                        <select name="shift_id" required
                            class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3">

                            <option value="">
                                Pilih kasir...
                            </option>

                            @foreach ($todayShifts as $s)
                                <option value="{{ $s->id }}">

                                    {{ $s->karyawan?->nama ?? 'Kasir' }}
                                    —
                                    sesi {{ ucfirst($s->sesi) }}
                                    ({{ substr($s->jam_mulai, 0, 5) }}
                                    -
                                    {{ substr($s->jam_selesai, 0, 5) }})
                                </option>
                            @endforeach

                        </select>

                    </div>

                    <button type="submit"
                        class="w-full rounded-2xl bg-indigo-600 px-4 py-3 text-white hover:bg-indigo-700">

                        Pilih & Lanjut ke Absensi

                    </button>

                </form>
            @else
                {{-- MODE VERIFIKASI WAJAH --}}

                <h1 class="text-3xl font-semibold text-slate-900">
                    Verifikasi Wajah
                </h1>

                <p class="mt-2 text-slate-600">

                    Kasir:
                    <strong>
                        {{ $shift->karyawan->nama }}
                    </strong>

                </p>

                <p class="text-slate-500">

                    Cabang:
                    {{ $shift->cabang->namaCabang }}

                </p>

                <div class="mt-8">

                    <video id="video" autoplay playsinline class="w-full rounded-2xl bg-slate-200">
                    </video>

                </div>

                <button id="startVerification"
                    class="mt-6 w-full rounded-2xl bg-emerald-600 px-4 py-3 text-white hover:bg-emerald-700">

                    Mulai Verifikasi Wajah

                </button>

            @endif

        </div>

    </div>

</x-layouts.app>
