@props(['title' => 'Absensi Kasir'])

<x-layouts.app :title="$title">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red:        '#C0271A',
                            'red-dk':   '#96281B',
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

    @php
        // Controller kirim $activeAttendances (Collection), bukan $shift
        // $shift = attendance aktif pertama (bisa null kalau belum absen)
        $shift = $activeAttendances->first() ?? null;
        $faceDescriptorRaw = $shift?->karyawan?->face_descriptor ?? null;
    @endphp

    <div class="min-h-screen bg-brand-cream flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">

            {{-- Brand --}}
            <div class="flex justify-center mb-8">
                @if(isset($identitas) && $identitas->logo)
                    <img src="{{ asset('storage/' . $identitas->logo) }}" alt="{{ $identitas->nama_brand ?? 'Logo' }}"class="h-20 w-auto">
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-xl shadow-brand-red/10 overflow-hidden border border-brand-cream-dk">

                {{-- Red Header --}}
                <div class="bg-brand-red-dk px-6 py-5 flex items-center gap-3">
                    {{-- <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">
                        {{ $shift ? '🪪' : '🕐' }}
                    </div> --}}
                    <div>
                        <h1 class="text-lg font-extrabold tracking-widest uppercase text-white">
                            {{ $shift ? 'Verifikasi Wajah' : 'Absensi Masuk' }}
                        </h1>
                        <p class="text-xs text-red-200 mt-0.5">
                            {{ now()->translatedFormat('l, d F Y') }} · {{ now()->format('H:i') }}
                        </p>
                    </div>
                </div>

                <div class="p-5 space-y-4">

                    @if (session('success'))
                        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-800 text-sm font-semibold">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-brand-red text-sm font-semibold space-y-1">
                            @foreach ($errors->all() as $error)
                                <div>⚠️ {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if (!$shift)
                    
                        {{-- ── MODE PILIH KARYAWAN ── --}}

                        @if ($karyawans->isEmpty())
                            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-4 text-center text-amber-800 text-sm font-semibold">
                                ⚠️ Semua karyawan sudah absen masuk hari ini.
                            </div>
                        @else
                            <p class="text-xs text-gray-500">Pilih karyawan yang akan absen masuk.</p>

                            <form method="POST" action="{{ route('attendance.clock-in') }}" id="absen-form">
                                @csrf

                                {{-- Hidden fields diisi JS setelah scan wajah --}}
                                <input type="hidden" name="karyawan_id" id="karyawan_id">
                                <input type="hidden" name="face_descriptor" id="face_descriptor">
                                <input type="hidden" name="foto_base64" id="foto_base64">

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                                        Nama Karyawan
                                    </label>
                                    <select id="karyawan-select" required
                                            class="w-full border border-brand-cream-dk rounded-xl px-4 py-2.5 text-sm bg-brand-cream focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition appearance-none">
                                        <option value="">Pilih karyawan...</option>
                                        @foreach ($karyawans as $k)
                                            <option value="{{ $k->idKaryawan }}"
                                                    data-descriptor="{{ $k->face_descriptor }}">
                                                {{ $k->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Kamera (muncul setelah pilih karyawan) --}}
                                <div id="camera-section" class="hidden space-y-3">
                                    <div class="relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3]">
                                        <video id="video" autoplay muted playsinline
                                               class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                                        <canvas id="canvas" class="hidden"></canvas>

                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                            <div id="face-guide"
                                                 class="w-40 h-52 rounded-full border-[2.5px] border-white/40 transition-colors duration-300"
                                                 style="box-shadow:0 0 0 9999px rgba(0,0,0,0.4)"></div>
                                        </div>

                                        <div id="camera-status"
                                             class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 text-white text-[11px] font-medium px-3 py-1 rounded-full backdrop-blur-sm whitespace-nowrap">
                                            Memuat model...
                                        </div>

                                        <div id="loading-models"
                                             class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center gap-3 text-white text-sm">
                                            <div class="w-8 h-8 rounded-full border-[3px] border-white/30 border-t-white animate-spin"></div>
                                            <span>Memuat model wajah...</span>
                                        </div>
                                    </div>

                                    {{-- Confidence Bar --}}
                                    <div id="confidence-wrap" class="hidden space-y-1">
                                        <div class="flex justify-between text-xs font-semibold text-gray-500">
                                            <span>Kecocokan Wajah</span>
                                            <span id="confidence-pct">0%</span>
                                        </div>
                                        <div class="h-2 bg-brand-cream-dk rounded-full overflow-hidden">
                                            <div id="confidence-fill"
                                                 class="h-full rounded-full transition-all duration-300"
                                                 style="width:0%;background:#ef4444"></div>
                                        </div>
                                    </div>

                                    <div id="status-box" class="hidden rounded-xl px-4 py-3 text-sm font-semibold text-center border"></div>

                                    <button type="button" id="btn-absen" disabled onclick="doAbsen()"
                                            class="w-full py-3.5 rounded-xl bg-brand-red hover:bg-brand-red-dk disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold text-sm tracking-[0.1em] uppercase transition-colors flex items-center justify-center gap-2">
                                        📸 Absen Sekarang
                                    </button>
                                </div>

                            </form>
                        @endif

                    @else
    {{-- ── MODE ABSEN PULANG (ada shift aktif) ── --}}

    @if ($activeAttendances->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-4 text-center text-amber-800 text-sm font-semibold">
            ⚠️ Tidak ada karyawan yang sedang shift aktif.
        </div>
    @else
        <p class="text-xs text-gray-500">Pilih karyawan yang akan absen pulang.</p>

        <form method="POST" action="{{ route('attendance.clock-out') }}" id="pulang-form">
            @csrf
            <input type="hidden" name="attendance_id" id="attendance_id">
            <input type="hidden" name="face_descriptor" id="face_descriptor">
            <input type="hidden" name="foto_base64" id="foto_base64">

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
                            data-descriptor="{{ $att->karyawan->face_descriptor }}"
                            data-masuk="{{ $att->jam_masuk?->format('H:i') }}"
                            data-nama="{{ $att->karyawan->nama }}"
                        >
                            {{ $att->karyawan->nama }} (masuk {{ $att->jam_masuk?->format('H:i') }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Info karyawan terpilih --}}
            <div id="info-karyawan" class="hidden flex items-center gap-3 bg-brand-cream border border-brand-cream-dk rounded-xl px-4 py-3">
                <div class="w-9 h-9 bg-brand-red/10 rounded-lg flex items-center justify-center text-lg">👤</div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Shift Aktif</p>
                    <p class="text-sm font-bold text-gray-800" id="info-nama">-</p>
                    <p class="text-xs text-gray-400">Masuk: <span id="info-masuk">-</span></p>
                </div>
            </div>

            {{-- Kamera (muncul setelah pilih) --}}
            <div id="camera-section-pulang" class="hidden space-y-3">

                <div id="no-face-id-warn" class="hidden bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-amber-800 text-sm font-semibold text-center">
                    ⚠️ Face ID karyawan ini belum terdaftar.
                </div>

                <div id="camera-wrap" class="hidden space-y-3">
                    <div class="relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3]">
                        <video id="video" autoplay muted playsinline
                               class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                        <canvas id="canvas" class="hidden"></canvas>

                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div id="face-guide"
                                 class="w-40 h-52 rounded-full border-[2.5px] border-white/40 transition-colors duration-300"
                                 style="box-shadow:0 0 0 9999px rgba(0,0,0,0.4)"></div>
                        </div>

                        <div id="camera-status"
                             class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 text-white text-[11px] font-medium px-3 py-1 rounded-full backdrop-blur-sm whitespace-nowrap">
                            Memuat model...
                        </div>

                        <div id="loading-models"
                             class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center gap-3 text-white text-sm">
                            <div class="w-8 h-8 rounded-full border-[3px] border-white/30 border-t-white animate-spin"></div>
                            <span>Memuat model wajah...</span>
                        </div>
                    </div>

                    {{-- Confidence Bar --}}
                    <div id="confidence-wrap" class="hidden space-y-1">
                        <div class="flex justify-between text-xs font-semibold text-gray-500">
                            <span>Kecocokan Wajah</span>
                            <span id="confidence-pct">0%</span>
                        </div>
                        <div class="h-2 bg-brand-cream-dk rounded-full overflow-hidden">
                            <div id="confidence-fill"
                                 class="h-full rounded-full transition-all duration-300"
                                 style="width:0%;background:#ef4444"></div>
                        </div>
                    </div>

                    <div id="status-box" class="hidden rounded-xl px-4 py-3 text-sm font-semibold text-center border"></div>

                    <button type="button" id="btn-absen" disabled onclick="doPulang()"
                            class="w-full py-3.5 rounded-xl bg-brand-red hover:bg-brand-red-dk disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold text-sm tracking-[0.1em] uppercase transition-colors flex items-center justify-center gap-2">
                        📸 Absen Pulang
                    </button>
                </div>
            </div>

        </form>
    @endif

    {{-- Karyawan lain yang belum absen masuk --}}
    @if ($karyawans->isNotEmpty())
        <div class="border-t border-brand-cream-dk pt-4">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Karyawan belum absen masuk</p>
            @foreach ($karyawans as $k)
                <div class="flex items-center gap-2 py-1.5 text-sm text-gray-600">
                    <span class="w-2 h-2 rounded-full bg-brand-cream-dk inline-block"></span>
                    {{ $k->nama }}
                </div>
            @endforeach
        </div>
    @endif

@endif

                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">© {{ date('Y') }} Tahu Bakso Morojoyo</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <script>
        const MODEL_URL       = '/face-models';
        const MATCH_THRESHOLD = 0.60;

        // Elemen DOM
        const video       = document.getElementById('video');
        const canvas      = document.getElementById('canvas');
        const guide       = document.getElementById('face-guide');
        const camStatus   = document.getElementById('camera-status');
        const loadingEl   = document.getElementById('loading-models');
        const btnAbsen    = document.getElementById('btn-absen');
        const statusBox   = document.getElementById('status-box');
        const confWrap    = document.getElementById('confidence-wrap');
        const confFill    = document.getElementById('confidence-fill');
        const confPct     = document.getElementById('confidence-pct');

        let faceDetected = false, lastDescriptor = null, currentDistance = 1.0;
        let faceMatchOk = false, detectionLoop = null;
        let registeredDescriptor = null;

        // ── MODE MASUK: inisialisasi setelah pilih karyawan ──
        @if (!$shift)
        const karyawanSelect = document.getElementById('karyawan-select');
        const cameraSection  = document.getElementById('camera-section');
        let currentStream = null;

        karyawanSelect?.addEventListener('change', async function () {
            const opt = this.options[this.selectedIndex];
            const rawDescriptor = opt?.dataset?.descriptor;

            if (!this.value || !rawDescriptor) {
                cameraSection.classList.add('hidden');
                stopCamera();
                return;
            }

            try {
                const parsed = JSON.parse(rawDescriptor);
                registeredDescriptor = new Float32Array(parsed);
            } catch {
                showStatus('err', '⚠️ Data Face ID karyawan ini tidak valid.');
                return;
            }

            document.getElementById('karyawan_id').value = this.value;
            cameraSection.classList.remove('hidden');
            await startCamera();
        });

        function stopCamera() {
            if (currentStream) { currentStream.getTracks().forEach(t => t.stop()); currentStream = null; }
            clearInterval(detectionLoop);
        }

        async function startCamera() {
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                ]);
                currentStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode:'user', width:640, height:480 } });
                video.srcObject = currentStream;
                await new Promise(r => video.addEventListener('loadedmetadata', r, { once:true }));
                canvas.width = video.videoWidth; canvas.height = video.videoHeight;
                loadingEl.style.display = 'none';
                camStatus.textContent = 'Arahkan wajah ke kamera';
                startLoop();
            } catch (err) {
                loadingEl.innerHTML = `<span class="text-red-300">⚠️ ${err.message || 'Gagal akses kamera'}</span>`;
            }
        }

        async function doAbsen() {
            if (!faceMatchOk) return;
            clearInterval(detectionLoop);
            btnAbsen.disabled = true;
            btnAbsen.innerHTML = '<div class="w-5 h-5 border-2 border-white/40 border-t-white rounded-full animate-spin mr-2"></div> Menyimpan...';
            const ctx = canvas.getContext('2d');
            ctx.save(); ctx.scale(-1,1); ctx.drawImage(video,-canvas.width,0,canvas.width,canvas.height); ctx.restore();
            document.getElementById('face_descriptor').value = JSON.stringify(Array.from(lastDescriptor));
            document.getElementById('foto_base64').value = canvas.toDataURL('image/jpeg', 0.8);
            document.getElementById('absen-form').submit();
        }
        @endif

        // ── MODE PULANG ──
        @if ($shift && $faceDescriptorRaw)
        const RAW = @json(is_string($faceDescriptorRaw) ? json_decode($faceDescriptorRaw, true) : $faceDescriptorRaw);
        registeredDescriptor = new Float32Array(RAW);

        (async function initPulang() {
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                ]);
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode:'user', width:640, height:480 } });
                video.srcObject = stream;
                await new Promise(r => video.addEventListener('loadedmetadata', r, { once:true }));
                canvas.width = video.videoWidth; canvas.height = video.videoHeight;
                loadingEl.style.display = 'none';
                camStatus.textContent = 'Arahkan wajah ke kamera';
                startLoop();
            } catch (err) {
                loadingEl.innerHTML = `<span class="text-red-300">⚠️ ${err.message || 'Gagal akses kamera'}</span>`;
            }
        })();

        async function doPulang() {
            if (!faceMatchOk) return;
            clearInterval(detectionLoop);
            btnAbsen.disabled = true;
            btnAbsen.innerHTML = '<div class="w-5 h-5 border-2 border-white/40 border-t-white rounded-full animate-spin mr-2"></div> Menyimpan...';
            const ctx = canvas.getContext('2d');
            ctx.save(); ctx.scale(-1,1); ctx.drawImage(video,-canvas.width,0,canvas.width,canvas.height); ctx.restore();
            document.getElementById('face_descriptor').value = JSON.stringify(Array.from(lastDescriptor));
            document.getElementById('foto_base64').value = canvas.toDataURL('image/jpeg', 0.8);
            document.getElementById('pulang-form').submit();
        }
        @endif

        // ── Detection loop (shared) ──
        function startLoop() {
            detectionLoop = setInterval(async () => {
                try {
                    const det = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.5 }))
                        .withFaceLandmarks(true).withFaceDescriptor();

                    if (det && registeredDescriptor) {
                        faceDetected   = true;
                        lastDescriptor = det.descriptor;
                        currentDistance = faceapi.euclideanDistance(lastDescriptor, registeredDescriptor);

                        const pct = Math.max(0, Math.min(100, Math.round((1 - currentDistance / MATCH_THRESHOLD) * 100)));
                        confWrap?.classList.remove('hidden');
                        if (confPct) confPct.textContent = pct + '%';
                        if (confFill) {
                            confFill.style.width = pct + '%';
                            confFill.style.background = currentDistance <= MATCH_THRESHOLD ? '#10b981' : currentDistance <= 0.75 ? '#f59e0b' : '#ef4444';
                        }

                        if (currentDistance <= MATCH_THRESHOLD) {
                            faceMatchOk = true;
                            guide.style.borderColor = '#34d399';
                            camStatus.textContent   = '✓ Wajah cocok — siap absen';
                            if (btnAbsen) btnAbsen.disabled = false;
                        } else {
                            faceMatchOk = false;
                            guide.style.borderColor = '#fbbf24';
                            camStatus.textContent   = `Wajah belum cocok (${currentDistance.toFixed(2)})`;
                            if (btnAbsen) btnAbsen.disabled = true;
                        }
                    } else {
                        faceDetected = false; faceMatchOk = false; lastDescriptor = null; currentDistance = 1.0;
                        guide.style.borderColor = 'rgba(255,255,255,0.4)';
                        camStatus.textContent   = 'Wajah tidak terdeteksi...';
                        if (btnAbsen) btnAbsen.disabled = true;
                        confWrap?.classList.remove('hidden');
                        if (confFill) { confFill.style.width = '0%'; confFill.style.background = '#ef4444'; }
                        if (confPct) confPct.textContent = '0%';
                    }
                } catch(_) {}
            }, 600);
        }

        function showStatus(type, msg) {
            if (!statusBox) return;
            const cls = {
                ok:   'bg-green-50 border-green-200 text-green-800',
                err:  'bg-red-50 border-red-200 text-brand-red',
                warn: 'bg-amber-50 border-amber-200 text-amber-800',
                wait: 'bg-blue-50 border-blue-200 text-blue-800',
            };
            statusBox.className = 'rounded-xl px-4 py-3 text-sm font-semibold text-center border ' + (cls[type] || '');
            statusBox.innerHTML = msg;
            statusBox.classList.remove('hidden');
        }
    </script>

</x-layouts.app>