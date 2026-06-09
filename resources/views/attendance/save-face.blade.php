@extends('layouts.app')

@section('title', 'Setup Wajah')

@section('content')
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
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: #FAF6EF; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin .8s linear infinite; }
    </style>

    <div class="min-h-screen bg-brand-cream flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">

            {{-- Brand --}}
            <div class="text-center mb-5">
                <p class="text-xs font-bold tracking-[0.2em] text-brand-red/40 uppercase">· Tahu Bakso Morojoyo ·</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl shadow-brand-red/10 overflow-hidden border border-brand-cream-dk">

                {{-- Red Header --}}
                <div class="bg-gradient-to-br from-brand-red to-brand-red-dk px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">🪪</div>
                        <div>
                            <h1 class="text-lg font-extrabold tracking-widest uppercase text-white">Setup Wajah</h1>
                            <p class="text-xs text-red-200 mt-0.5">Ambil 3 foto dari sudut berbeda untuk akurasi terbaik</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4">

                    {{-- Step Indicator --}}
                    <div class="flex items-center gap-0 mb-1">
                        @foreach([['1','Foto'],['2','Verifikasi'],['3','Simpan']] as $i => $step)
                            <div class="flex items-center flex-1" id="step-wrap-{{ $i+1 }}">
                                <div class="flex flex-col items-center flex-1">
                                    <div id="step-dot-{{ $i+1 }}"
                                         class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300
                                         {{ $i === 0 ? 'bg-brand-red border-brand-red text-white' : 'bg-white border-brand-cream-dk text-gray-400' }}">
                                        {{ $step[0] }}
                                    </div>
                                    <span id="step-label-{{ $i+1 }}" class="text-[10px] font-bold mt-1 {{ $i === 0 ? 'text-brand-red' : 'text-gray-400' }}">{{ $step[1] }}</span>
                                </div>
                                @if($i < 2)
                                    <div class="h-0.5 flex-1 bg-brand-cream-dk mb-4"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Progress Bar --}}
                    <div class="h-1 bg-brand-cream-dk rounded-full overflow-hidden">
                        <div id="progress-fill" class="h-full bg-brand-red rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>

                    {{-- Camera --}}
                    <div class="relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3]">
                        <video id="sf-video" autoplay muted playsinline class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                        <canvas id="sf-canvas" class="hidden"></canvas>

                        {{-- Face Guide --}}
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div id="cam-guide"
                                 class="w-40 h-52 rounded-full border-[2.5px] border-white/40 transition-colors duration-300"
                                 style="box-shadow: 0 0 0 9999px rgba(0,0,0,0.4)"></div>
                        </div>

                        {{-- Status Pill --}}
                        <div id="cam-badge"
                             class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 text-white text-[11px] font-medium px-3 py-1 rounded-full backdrop-blur-sm whitespace-nowrap">
                            Memuat model...
                        </div>

                        {{-- Loading Overlay --}}
                        <div id="cam-loading"
                             class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center gap-3 text-white text-sm">
                            <div class="spinner w-8 h-8 rounded-full border-[3px] border-white/30 border-t-white"></div>
                            <span>Memuat model pengenalan wajah...</span>
                        </div>
                    </div>

                    {{-- Capture Slots --}}
                    <div class="grid grid-cols-3 gap-2" id="captures">
                        @for ($i = 0; $i < 3; $i++)
                            <div id="slot-{{ $i }}"
                                 class="aspect-square rounded-xl bg-brand-cream border-2 border-dashed border-brand-cream-dk flex items-center justify-center text-2xl overflow-hidden transition-all">
                                📷
                            </div>
                        @endfor
                    </div>

                    {{-- Hint --}}
                    <p id="hint-text" class="text-xs text-gray-500 text-center leading-relaxed">
                        Hadapkan wajah ke kamera, lalu klik tombol foto. Ambil 3 foto dari sudut sedikit berbeda.
                    </p>

                    {{-- Capture Button --}}
                    <button id="btn-capture" disabled onclick="capturePhoto()"
                            class="w-full py-3.5 rounded-xl bg-brand-red hover:bg-brand-red-dk disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold text-sm tracking-[0.1em] uppercase transition-all flex items-center justify-center gap-2">
                        Ambil Foto (<span id="capture-count">0</span>/3)
                    </button>

                    {{-- Save Button --}}
                    <button id="btn-save" onclick="saveToServer()"
                            class="w-full py-3.5 rounded-xl bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold text-sm tracking-[0.1em] uppercase transition-all hidden flex items-center justify-center gap-2">
                        ✅ Simpan Data Wajah
                    </button>

                    {{-- Reset Button --}}
                    <button id="btn-reset" onclick="resetCaptures()"
                            class="w-full py-2.5 rounded-xl border border-brand-cream-dk bg-transparent text-gray-400 hover:text-gray-600 text-sm font-semibold transition-colors hidden">
                        Ulangi dari awal
                    </button>

                    {{-- Status Box --}}
                    <div id="status-box" class="hidden rounded-xl px-4 py-3 text-sm font-semibold text-center border"></div>

                </div>
            </div>

            {{-- Back --}}
            <div class="text-center mt-4">
                <a href="{{ url()->previous() }}" class="text-xs font-semibold text-brand-red/50 hover:text-brand-red transition-colors">
                    ← Kembali
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const SAVE_URL = "{{ route('attendance.save-face') }}";
        const MODEL_URL = '/face-models';
        const NEEDED = 3;

        let captures = [], faceDetected = false, lastDescriptor = null, detectionLoop = null;

        const video    = document.getElementById('sf-video');
        const canvas   = document.getElementById('sf-canvas');
        const guide    = document.getElementById('cam-guide');
        const badge    = document.getElementById('cam-badge');
        const loading  = document.getElementById('cam-loading');
        const btnCap   = document.getElementById('btn-capture');
        const btnSave  = document.getElementById('btn-save');
        const btnReset = document.getElementById('btn-reset');
        const statusBox= document.getElementById('status-box');
        const countEl  = document.getElementById('capture-count');
        const hintEl   = document.getElementById('hint-text');
        const progress = document.getElementById('progress-fill');

        function showStatus(type, msg) {
            const classes = {
                ok:   'bg-green-50 text-green-800 border-green-200',
                err:  'bg-red-50 text-brand-red border-red-200',
                wait: 'bg-amber-50 text-amber-800 border-amber-200',
            };
            statusBox.className = 'rounded-xl px-4 py-3 text-sm font-semibold text-center border ' + (classes[type] || classes.wait);
            statusBox.innerHTML = msg;
            statusBox.classList.remove('hidden');
        }

        function updateStep(n) {
            for (let i = 1; i <= 3; i++) {
                const dot   = document.getElementById('step-dot-' + i);
                const label = document.getElementById('step-label-' + i);
                if (i < n) {
                    dot.className   = 'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300 bg-green-500 border-green-500 text-white';
                    dot.textContent = '✓';
                    label.className = 'text-[10px] font-bold mt-1 text-green-600';
                } else if (i === n) {
                    dot.className   = 'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300 bg-brand-red border-brand-red text-white';
                    dot.textContent = i;
                    label.className = 'text-[10px] font-bold mt-1 text-brand-red';
                } else {
                    dot.className   = 'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300 bg-white border-brand-cream-dk text-gray-400';
                    dot.textContent = i;
                    label.className = 'text-[10px] font-bold mt-1 text-gray-400';
                }
            }
        }

        async function initFaceApi() {
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                ]);
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
                video.srcObject = stream;
                await new Promise(r => video.addEventListener('loadedmetadata', r, { once: true }));
                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;
                loading.style.display = 'none';
                badge.textContent = 'Arahkan wajah ke kamera';
                startLoop();
            } catch (err) {
                loading.innerHTML = `<span class="text-red-300">⚠️ ${err.message || 'Gagal akses kamera'}</span>`;
            }
        }

        function startLoop() {
            detectionLoop = setInterval(async () => {
                try {
                    const det = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.5 }))
                        .withFaceLandmarks(true).withFaceDescriptor();
                    if (det) {
                        faceDetected = true;
                        lastDescriptor = Array.from(det.descriptor);
                        guide.style.borderColor = '#22c55e';
                        badge.textContent = captures.length < NEEDED ? '✓ Wajah terdeteksi — siap foto' : '✓ Semua foto diambil';
                        if (captures.length < NEEDED) btnCap.disabled = false;
                    } else {
                        faceDetected = false; lastDescriptor = null;
                        guide.style.borderColor = 'rgba(255,255,255,0.4)';
                        badge.textContent = 'Arahkan wajah ke kamera...';
                        btnCap.disabled = true;
                    }
                } catch (_) {}
            }, 500);
        }

        function capturePhoto() {
            if (!faceDetected || captures.length >= NEEDED) return;
            const ctx = canvas.getContext('2d');
            ctx.save(); ctx.scale(-1, 1);
            ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
            ctx.restore();
            const base64 = canvas.toDataURL('image/jpeg', 0.8);
            captures.push({ base64, descriptor: [...lastDescriptor] });

            const slot = document.getElementById('slot-' + (captures.length - 1));
            slot.innerHTML = `<img src="${base64}" class="w-full h-full object-cover">
                              <span class="absolute top-1 right-1 bg-black/60 text-green-400 text-[9px] font-bold px-1.5 py-0.5 rounded">${captures.length}</span>`;
            slot.classList.add('relative', 'border-green-400', 'border-solid');
            slot.classList.remove('border-dashed', 'border-brand-cream-dk');

            countEl.textContent = captures.length;
            progress.style.width = ((captures.length / NEEDED) * 66) + '%';

            if (captures.length >= NEEDED) {
                clearInterval(detectionLoop);
                btnCap.disabled = true;
                btnSave.classList.remove('hidden');
                btnReset.classList.remove('hidden');
                hintEl.textContent = '3 foto berhasil diambil! Klik "Simpan Data Wajah" untuk menyimpan.';
                updateStep(2);
                showStatus('wait', '⏳ Siap disimpan. Klik tombol simpan di bawah.');
            } else {
                const hints = ['Bagus! Sekarang miringkan kepala sedikit ke kiri.', 'Hampir selesai! Miringkan sedikit ke kanan.'];
                hintEl.textContent = hints[captures.length - 1] || 'Ambil foto berikutnya.';
            }
        }

        async function saveToServer() {
            if (captures.length < NEEDED) return;
            btnSave.disabled = true;
            btnSave.textContent = '⏳ Menyimpan...';
            showStatus('wait', '⏳ Mengirim data wajah ke server...');
            updateStep(3);
            const avgDescriptor = captures[0].descriptor.map((_, i) =>
                captures.reduce((sum, c) => sum + c.descriptor[i], 0) / captures.length
            );
            try {
                const res = await fetch(SAVE_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ face_descriptor: avgDescriptor, foto_base64: captures[0].base64 }),
                });
                const data = await res.json();
                if (data.success) {
                    progress.style.width = '100%';
                    showStatus('ok', '✅ Data wajah berhasil disimpan! Kamu sekarang bisa absen.');
                    btnSave.classList.add('hidden');
                    btnReset.classList.add('hidden');
                    setTimeout(() => { window.location.href = '/absensi'; }, 2500);
                } else {
                    showStatus('err', '❌ ' + (data.message || 'Gagal menyimpan.'));
                    btnSave.disabled = false;
                    btnSave.textContent = '✅ Simpan Data Wajah';
                }
            } catch (err) {
                showStatus('err', '❌ Koneksi bermasalah. Coba lagi.');
                btnSave.disabled = false;
                btnSave.textContent = '✅ Simpan Data Wajah';
            }
        }

        function resetCaptures() {
            captures = [];
            for (let i = 0; i < NEEDED; i++) {
                const slot = document.getElementById('slot-' + i);
                slot.innerHTML = '📷';
                slot.className = 'aspect-square rounded-xl bg-brand-cream border-2 border-dashed border-brand-cream-dk flex items-center justify-center text-2xl overflow-hidden transition-all';
            }
            countEl.textContent = '0';
            btnCap.disabled = !faceDetected;
            btnSave.classList.add('hidden');
            btnReset.classList.add('hidden');
            statusBox.classList.add('hidden');
            hintEl.textContent = 'Hadapkan wajah ke kamera, lalu klik tombol foto. Ambil 3 foto dari sudut sedikit berbeda.';
            progress.style.width = '0%';
            updateStep(1);
            startLoop();
        }

        initFaceApi();
    </script>
@endsection