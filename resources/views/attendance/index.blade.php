@props(['title' => 'Absensi Kasir'])

@php
    $activeAttendances = $activeAttendances ?? collect(isset($activeAttendance) && $activeAttendance ? [$activeAttendance] : []);
    $karyawans = $karyawans ?? collect();
    $hasActiveShifts = $activeAttendances->isNotEmpty();
    $showPulang = request()->query('mode') === 'pulang';
@endphp

<x-layouts.app :title="$title">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red: '#C0271A',
                            'red-dk': '#96281B',
                            cream: '#FAF6EF',
                            'cream-dk': '#F0E9DC',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #FAF6EF;
        }
    </style>

    <div class="min-h-screen bg-brand-cream flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md space-y-5">

            {{-- Brand / Logo --}}
            <div class="flex justify-center mb-4">
                @if(isset($identitas) && $identitas->logo)
                    <img src="{{ asset('storage/' . $identitas->logo) }}" alt="{{ $identitas->nama_brand ?? 'Logo' }}" class="h-20 w-auto">
                @endif
            </div>

            {{-- Alert global --}}
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-800 text-sm font-semibold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-brand-red text-sm font-semibold">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-brand-red text-sm font-semibold space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>⚠️ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Karyawan sedang shift --}}
            @if ($hasActiveShifts)
                <div class="bg-white rounded-2xl shadow-xl shadow-brand-red/10 overflow-hidden border border-brand-cream-dk">
                    <div class="bg-brand-red-dk px-6 py-5">
                        <h1 class="text-lg font-extrabold tracking-widest uppercase text-white">Karyawan Sedang Shift</h1>
                        <p class="text-xs text-red-200 mt-0.5">
                            {{ now()->translatedFormat('l, d F Y') }} · {{ now()->format('H:i') }}
                        </p>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="space-y-2">
                            @foreach ($activeAttendances as $attendance)
                                <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                                    <div class="text-sm font-bold text-green-800">
                                        {{ $attendance->karyawan?->nama ?? '-' }}
                                    </div>
                                    <div class="text-xs text-green-700 mt-1">
                                        Jam masuk: {{ $attendance->jam_masuk?->format('H:i') ?? '-' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <a href="{{ route('cashier.pos') }}"
                           class="w-full flex items-center justify-center py-3.5 rounded-xl bg-brand-red hover:bg-brand-red-dk text-white font-extrabold text-sm tracking-[0.1em] uppercase transition-colors">
                            Lanjut ke POS
                        </a>

                        <a href="{{ route('attendance.index') }}?mode=pulang"
                           class="w-full flex items-center justify-center py-3.5 rounded-xl bg-gray-700 hover:bg-gray-800 text-white font-extrabold text-sm tracking-[0.1em] uppercase transition-colors">
                            Selesai Shift / Absen Pulang
                        </a>

                        @if ($showPulang)
                            <a href="{{ route('attendance.index') }}"
                               class="w-full flex items-center justify-center py-3 rounded-xl bg-brand-cream border border-brand-cream-dk text-gray-700 font-bold text-sm transition-colors">
                                Kembali ke Absen Masuk
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Absensi masuk: tetap muncul kalau masih ada karyawan lain yang belum absen dan bukan mode pulang --}}
            @if (!$showPulang && $karyawans->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-xl shadow-brand-red/10 overflow-hidden border border-brand-cream-dk">
                    <div class="bg-brand-red-dk px-6 py-5 flex items-center gap-3">
                        <div>
                            <h1 class="text-lg font-extrabold tracking-widest uppercase text-white">Absensi Masuk</h1>
                            <p class="text-xs text-red-200 mt-0.5">
                                {{ now()->translatedFormat('l, d F Y') }} · {{ now()->format('H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <p class="text-xs text-gray-500">Pilih karyawan berikutnya yang akan absen masuk.</p>

                        <form method="POST" action="{{ route('attendance.clock-in') }}" id="absen-form">
                            @csrf
                            <input type="hidden" name="karyawan_id" id="karyawan_id">
                            <input type="hidden" name="face_descriptor" id="masuk_face_descriptor">
                            <input type="hidden" name="foto_base64" id="masuk_foto_base64">

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                                    Nama Karyawan
                                </label>
                                <select id="karyawan-select" required
                                        class="w-full border border-brand-cream-dk rounded-xl px-4 py-2.5 text-sm bg-brand-cream focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition appearance-none">
                                    <option value="">Pilih karyawan...</option>
                                    @foreach ($karyawans as $k)
                                        <option value="{{ $k->idKaryawan }}" data-descriptor="{{ $k->face_descriptor }}">
                                            {{ $k->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="masuk-camera-section" class="hidden space-y-3 mt-4">
                                <div class="relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3]">
                                    <video id="masuk-video" autoplay muted playsinline
                                           class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                                    <canvas id="masuk-canvas" class="hidden"></canvas>

                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div id="masuk-face-guide"
                                             class="w-40 h-52 rounded-full border-[2.5px] border-white/40 transition-colors duration-300"
                                             style="box-shadow:0 0 0 9999px rgba(0,0,0,0.4)"></div>
                                    </div>

                                    <div id="masuk-camera-status"
                                         class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 text-white text-[11px] font-medium px-3 py-1 rounded-full backdrop-blur-sm whitespace-nowrap">
                                        Memuat model...
                                    </div>

                                    <div id="masuk-loading-models"
                                         class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center gap-3 text-white text-sm">
                                        <div class="w-8 h-8 rounded-full border-[3px] border-white/30 border-t-white animate-spin"></div>
                                        <span>Memuat model wajah...</span>
                                    </div>
                                </div>

                                <div id="masuk-confidence-wrap" class="hidden space-y-1">
                                    <div class="flex justify-between text-xs font-semibold text-gray-500">
                                        <span>Kecocokan Wajah</span>
                                        <span id="masuk-confidence-pct">0%</span>
                                    </div>
                                    <div class="h-2 bg-brand-cream-dk rounded-full overflow-hidden">
                                        <div id="masuk-confidence-fill"
                                             class="h-full rounded-full transition-all duration-300"
                                             style="width:0%;background:#ef4444"></div>
                                    </div>
                                </div>

                                <button type="button" id="masuk-btn-absen" disabled onclick="doAbsen()"
                                        class="w-full py-3.5 rounded-xl bg-brand-red hover:bg-brand-red-dk disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold text-sm tracking-[0.1em] uppercase transition-colors flex items-center justify-center gap-2">
                                    📸 Absen Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Jika semua karyawan sudah absen masuk, tampilkan info singkat --}}
            @if (!$showPulang && $hasActiveShifts && $karyawans->isEmpty())
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-4 text-center text-amber-800 text-sm font-semibold">
                    Semua karyawan aktif sudah berada dalam shift.
                </div>
            @endif

            {{-- Absensi pulang hanya muncul saat mode=pulang --}}
            @if ($showPulang && $hasActiveShifts)
                <div class="bg-white rounded-2xl shadow-xl shadow-brand-red/10 overflow-hidden border border-brand-cream-dk">
                    <div class="bg-brand-red-dk px-6 py-5 flex items-center gap-3">
                        <div>
                            <h1 class="text-lg font-extrabold tracking-widest uppercase text-white">Absensi Pulang</h1>
                            <p class="text-xs text-red-200 mt-0.5">
                                {{ now()->translatedFormat('l, d F Y') }} · {{ now()->format('H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <p class="text-xs text-gray-500">Pilih karyawan yang akan absen pulang.</p>

                        <form method="POST" action="{{ route('attendance.clock-out') }}" id="pulang-form">
                            @csrf
                            <input type="hidden" name="attendance_id" id="attendance_id">
                            <input type="hidden" name="face_descriptor" id="pulang_face_descriptor">
                            <input type="hidden" name="foto_base64" id="pulang_foto_base64">

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                                    Nama Karyawan
                                </label>
                                <select id="pulang-select" required
                                        class="w-full border border-brand-cream-dk rounded-xl px-4 py-2.5 text-sm bg-brand-cream focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition appearance-none">
                                    <option value="">Pilih karyawan...</option>
                                    @foreach ($activeAttendances as $att)
                                        <option
                                            value="{{ $att->id }}"
                                            data-descriptor="{{ $att->karyawan?->face_descriptor }}"
                                            data-masuk="{{ $att->jam_masuk?->format('H:i') }}"
                                            data-nama="{{ $att->karyawan?->nama }}"
                                        >
                                            {{ $att->karyawan?->nama ?? '-' }} (masuk {{ $att->jam_masuk?->format('H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="pulang-info-karyawan" class="hidden items-center gap-3 bg-brand-cream border border-brand-cream-dk rounded-xl px-4 py-3 mt-3">
                                <div class="w-9 h-9 bg-brand-red/10 rounded-lg flex items-center justify-center text-lg">👤</div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Shift Aktif</p>
                                    <p class="text-sm font-bold text-gray-800" id="pulang-info-nama">-</p>
                                    <p class="text-xs text-gray-400">Masuk: <span id="pulang-info-masuk">-</span></p>
                                </div>
                            </div>

                            <div id="pulang-no-face-warn" class="hidden bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-amber-800 text-sm font-semibold text-center mt-3">
                                ⚠️ Face ID karyawan ini belum terdaftar.
                            </div>

                            <div id="pulang-camera-wrap" class="hidden space-y-3 mt-3">
                                <div class="relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3]">
                                    <video id="pulang-video" autoplay muted playsinline
                                           class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                                    <canvas id="pulang-canvas" class="hidden"></canvas>

                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div id="pulang-face-guide"
                                             class="w-40 h-52 rounded-full border-[2.5px] border-white/40 transition-colors duration-300"
                                             style="box-shadow:0 0 0 9999px rgba(0,0,0,0.4)"></div>
                                    </div>

                                    <div id="pulang-camera-status"
                                         class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 text-white text-[11px] font-medium px-3 py-1 rounded-full backdrop-blur-sm whitespace-nowrap">
                                        Memuat model...
                                    </div>

                                    <div id="pulang-loading-models"
                                         class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center gap-3 text-white text-sm">
                                        <div class="w-8 h-8 rounded-full border-[3px] border-white/30 border-t-white animate-spin"></div>
                                        <span>Memuat model wajah...</span>
                                    </div>
                                </div>

                                <div id="pulang-confidence-wrap" class="hidden space-y-1">
                                    <div class="flex justify-between text-xs font-semibold text-gray-500">
                                        <span>Kecocokan Wajah</span>
                                        <span id="pulang-confidence-pct">0%</span>
                                    </div>
                                    <div class="h-2 bg-brand-cream-dk rounded-full overflow-hidden">
                                        <div id="pulang-confidence-fill"
                                             class="h-full rounded-full transition-all duration-300"
                                             style="width:0%;background:#ef4444"></div>
                                    </div>
                                </div>

                                <button type="button" id="pulang-btn-absen" disabled onclick="doPulang()"
                                        class="w-full py-3.5 rounded-xl bg-brand-red hover:bg-brand-red-dk disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold text-sm tracking-[0.1em] uppercase transition-colors flex items-center justify-center gap-2">
                                    📸 Absen Pulang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if (!$hasActiveShifts && !$showPulang && $karyawans->isEmpty())
                <div class="bg-white rounded-2xl shadow-xl shadow-brand-red/10 overflow-hidden border border-brand-cream-dk">
                    <div class="bg-brand-red-dk px-6 py-5">
                        <h1 class="text-lg font-extrabold tracking-widest uppercase text-white">Absensi Kasir</h1>
                        <p class="text-xs text-red-200 mt-0.5">
                            {{ now()->translatedFormat('l, d F Y') }} · {{ now()->format('H:i') }}
                        </p>
                    </div>
                    <div class="p-5">
                        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-4 text-center text-amber-800 text-sm font-semibold">
                            ⚠️ Belum ada data karyawan yang tersedia untuk absensi.
                        </div>
                    </div>
                </div>
            @endif

            <p class="text-center text-xs text-gray-400 mt-6">© {{ date('Y') }} Tahu Bakso Morojoyo</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <script>
    (function () {
        const MODEL_URL = '/face-models';
        const MATCH_THRESHOLD = 0.60;

        let modelsLoaded = false;

        async function loadModels() {
            if (modelsLoaded) return;

            if (typeof faceapi === 'undefined') {
                alert('Library face-api.js belum berhasil dimuat.');
                return;
            }

            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            ]);

            modelsLoaded = true;
        }

        function makeDetector(ids) {
            const el = {
                video: document.getElementById(ids.video),
                canvas: document.getElementById(ids.canvas),
                guide: document.getElementById(ids.guide),
                status: document.getElementById(ids.status),
                loading: document.getElementById(ids.loading),
                confWrap: document.getElementById(ids.confWrap),
                confFill: document.getElementById(ids.confFill),
                confPct: document.getElementById(ids.confPct),
                btn: document.getElementById(ids.btn),
            };

            let stream = null;
            let loop = null;
            let registeredDescriptor = null;
            let faceMatchOk = false;
            let lastDescriptor = null;

            async function start(descriptor) {
                stop();
                registeredDescriptor = descriptor;
                faceMatchOk = false;
                lastDescriptor = null;

                if (!el.video || !el.canvas || !el.btn) return;

                el.btn.disabled = true;
                if (el.confWrap) el.confWrap.classList.add('hidden');
                if (el.loading) el.loading.style.display = 'flex';

                try {
                    await loadModels();

                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user', width: 640, height: 480 },
                        audio: false
                    });

                    el.video.srcObject = stream;

                    await new Promise(resolve => {
                        el.video.addEventListener('loadedmetadata', resolve, { once: true });
                    });

                    el.canvas.width = el.video.videoWidth || 640;
                    el.canvas.height = el.video.videoHeight || 480;

                    if (el.loading) el.loading.style.display = 'none';
                    if (el.status) el.status.textContent = 'Arahkan wajah ke kamera';

                    startLoop();
                } catch (err) {
                    if (el.loading) {
                        el.loading.innerHTML = `<span class="text-red-300">⚠️ ${err.message || 'Gagal akses kamera'}</span>`;
                    }
                }
            }

            function stop() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }

                if (loop) {
                    clearInterval(loop);
                    loop = null;
                }

                faceMatchOk = false;
                lastDescriptor = null;

                if (el.btn) el.btn.disabled = true;
                if (el.confWrap) el.confWrap.classList.add('hidden');
                if (el.loading) el.loading.style.display = 'flex';
            }

            function startLoop() {
                loop = setInterval(async () => {
                    try {
                        const detection = await faceapi
                            .detectSingleFace(el.video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.5 }))
                            .withFaceLandmarks()
                            .withFaceDescriptor();

                        if (detection && registeredDescriptor) {
                            lastDescriptor = detection.descriptor;

                            const distance = faceapi.euclideanDistance(lastDescriptor, registeredDescriptor);
                            const percent = Math.max(0, Math.min(100, Math.round((1 - distance / MATCH_THRESHOLD) * 100)));

                            if (el.confWrap) el.confWrap.classList.remove('hidden');
                            if (el.confPct) el.confPct.textContent = percent + '%';

                            if (el.confFill) {
                                el.confFill.style.width = percent + '%';
                                el.confFill.style.background = distance <= MATCH_THRESHOLD ? '#10b981' : '#ef4444';
                            }

                            if (distance <= MATCH_THRESHOLD) {
                                faceMatchOk = true;
                                if (el.guide) el.guide.style.borderColor = '#34d399';
                                if (el.status) el.status.textContent = '✓ Wajah cocok — siap absen';
                                if (el.btn) el.btn.disabled = false;
                            } else {
                                faceMatchOk = false;
                                if (el.guide) el.guide.style.borderColor = '#fbbf24';
                                if (el.status) el.status.textContent = `Wajah belum cocok (${distance.toFixed(2)})`;
                                if (el.btn) el.btn.disabled = true;
                            }
                        } else {
                            faceMatchOk = false;
                            lastDescriptor = null;

                            if (el.guide) el.guide.style.borderColor = 'rgba(255,255,255,0.4)';
                            if (el.status) el.status.textContent = 'Wajah tidak terdeteksi...';
                            if (el.btn) el.btn.disabled = true;

                            if (el.confWrap) el.confWrap.classList.remove('hidden');
                            if (el.confFill) {
                                el.confFill.style.width = '0%';
                                el.confFill.style.background = '#ef4444';
                            }
                            if (el.confPct) el.confPct.textContent = '0%';
                        }
                    } catch (error) {
                        console.warn(error);
                    }
                }, 600);
            }

            function capture() {
                if (!faceMatchOk || !lastDescriptor) return null;

                const context = el.canvas.getContext('2d');
                context.save();
                context.scale(-1, 1);
                context.drawImage(el.video, -el.canvas.width, 0, el.canvas.width, el.canvas.height);
                context.restore();

                return {
                    descriptor: JSON.stringify(Array.from(lastDescriptor)),
                    foto: el.canvas.toDataURL('image/jpeg', 0.8),
                };
            }

            function setSubmitting() {
                if (loop) {
                    clearInterval(loop);
                    loop = null;
                }

                if (el.btn) {
                    el.btn.disabled = true;
                    el.btn.innerHTML = '<div class="w-5 h-5 border-2 border-white/40 border-t-white rounded-full animate-spin mr-2"></div> Menyimpan...';
                }
            }

            return {
                start,
                stop,
                capture,
                setSubmitting,
                isMatch: () => faceMatchOk,
            };
        }

        @if (!$showPulang && $karyawans->isNotEmpty())
        (function () {
            const select = document.getElementById('karyawan-select');
            const cameraSection = document.getElementById('masuk-camera-section');

            const detector = makeDetector({
                video: 'masuk-video',
                canvas: 'masuk-canvas',
                guide: 'masuk-face-guide',
                status: 'masuk-camera-status',
                loading: 'masuk-loading-models',
                confWrap: 'masuk-confidence-wrap',
                confFill: 'masuk-confidence-fill',
                confPct: 'masuk-confidence-pct',
                btn: 'masuk-btn-absen',
            });

            select?.addEventListener('change', async function () {
                const option = this.options[this.selectedIndex];
                const rawDescriptor = option?.dataset?.descriptor;

                detector.stop();
                cameraSection.classList.add('hidden');

                if (!this.value || !rawDescriptor || rawDescriptor === 'null' || rawDescriptor === '[]') {
                    return;
                }

                let descriptor;

                try {
                    descriptor = new Float32Array(JSON.parse(rawDescriptor));
                } catch (error) {
                    alert('Data Face ID karyawan tidak valid. Daftarkan ulang Face ID di halaman owner.');
                    return;
                }

                document.getElementById('karyawan_id').value = this.value;
                cameraSection.classList.remove('hidden');
                await detector.start(descriptor);
            });

            window.doAbsen = async function () {
                if (!detector.isMatch()) return;

                const data = detector.capture();
                if (!data) return;

                detector.setSubmitting();
                document.getElementById('masuk_face_descriptor').value = data.descriptor;
                document.getElementById('masuk_foto_base64').value = data.foto;
                document.getElementById('absen-form').submit();
            };
        })();
        @endif

        @if ($showPulang && $hasActiveShifts)
        (function () {
            const select = document.getElementById('pulang-select');
            const infoBox = document.getElementById('pulang-info-karyawan');
            const infoNama = document.getElementById('pulang-info-nama');
            const infoMasuk = document.getElementById('pulang-info-masuk');
            const noFaceWarn = document.getElementById('pulang-no-face-warn');
            const cameraWrap = document.getElementById('pulang-camera-wrap');

            const detector = makeDetector({
                video: 'pulang-video',
                canvas: 'pulang-canvas',
                guide: 'pulang-face-guide',
                status: 'pulang-camera-status',
                loading: 'pulang-loading-models',
                confWrap: 'pulang-confidence-wrap',
                confFill: 'pulang-confidence-fill',
                confPct: 'pulang-confidence-pct',
                btn: 'pulang-btn-absen',
            });

            select?.addEventListener('change', async function () {
                const option = this.options[this.selectedIndex];

                detector.stop();
                infoBox.classList.add('hidden');
                noFaceWarn.classList.add('hidden');
                cameraWrap.classList.add('hidden');

                if (!this.value) return;

                infoNama.textContent = option.dataset.nama || '-';
                infoMasuk.textContent = option.dataset.masuk || '-';
                infoBox.classList.remove('hidden');
                infoBox.style.display = 'flex';

                document.getElementById('attendance_id').value = this.value;

                const rawDescriptor = option?.dataset?.descriptor;

                if (!rawDescriptor || rawDescriptor === 'null' || rawDescriptor === '[]') {
                    noFaceWarn.classList.remove('hidden');
                    return;
                }

                let descriptor;

                try {
                    descriptor = new Float32Array(JSON.parse(rawDescriptor));
                } catch (error) {
                    noFaceWarn.classList.remove('hidden');
                    return;
                }

                cameraWrap.classList.remove('hidden');
                await detector.start(descriptor);
            });

            window.doPulang = async function () {
                if (!detector.isMatch()) return;

                const data = detector.capture();
                if (!data) return;

                detector.setSubmitting();
                document.getElementById('pulang_face_descriptor').value = data.descriptor;
                document.getElementById('pulang_foto_base64').value = data.foto;
                document.getElementById('pulang-form').submit();
            };
        })();
        @endif

        window.addEventListener('beforeunload', function () {
            // kamera akan otomatis berhenti saat halaman pindah
        });
    })();
    </script>
</x-layouts.app>
