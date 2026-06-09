{{-- resources/views/attendance/setup-face.blade.php --}}
@extends('layouts.app')

@section('title', 'Setup Wajah')

@section('content')

    <div class="min-h-screen bg-[#FFF8E7] flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="flex justify-center mb-6">
                <div class="bg-[#C0271A] rounded-xl px-5 py-2.5 shadow-lg">
                    <p class="text-[#F5C518] text-[7px] tracking-[.2em] text-center leading-none font-black uppercase">· Tahu Bakso ·</p>
                    <p class="text-[#F5C518] text-2xl leading-none tracking-widest text-center font-black" style="font-family:'Bebas Neue',sans-serif">MOROJOYO</p>
                </div>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-3xl shadow-xl border-t-4 border-[#C0271A] overflow-hidden">

                {{-- Header --}}
                <div class="bg-[#C0271A] px-6 py-5 flex items-center gap-3">
                    <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center text-xl flex-shrink-0">🪪</div>
                    <div>
                        <h1 class="text-white font-black text-xl leading-tight" style="font-family:'Bebas Neue',sans-serif">SETUP WAJAH</h1>
                        <p class="text-red-200 text-xs">Daftarkan wajah untuk absensi</p>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-5">

                    {{-- Info --}}
                    <div class="bg-[#FFF8E7] border border-[#F5C518]/40 rounded-2xl px-4 py-3 flex items-start gap-2.5">
                        <span class="text-[#F5C518] text-lg flex-shrink-0">💡</span>
                        <p class="text-gray-600 text-xs leading-relaxed">Foto wajah diambil <strong class="text-[#C0271A]">3x</strong> dari sudut berbeda untuk akurasi pengenalan yang lebih baik.</p>
                    </div>

                    {{-- Step indicator --}}
                    <div class="relative flex items-center">
                        <div class="absolute left-7 right-7 h-0.5 bg-gray-200 top-3.5 z-0"></div>
                        <div class="relative z-10 flex justify-between w-full">
                            <div class="flex flex-col items-center gap-1" id="step-1">
                                <div class="w-7 h-7 rounded-full bg-[#C0271A] text-white text-xs font-black flex items-center justify-center shadow-md sf-dot">1</div>
                                <span class="text-[10px] text-[#C0271A] font-bold sf-label">Foto</span>
                            </div>
                            <div class="flex flex-col items-center gap-1" id="step-2">
                                <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 text-xs font-black flex items-center justify-center sf-dot">2</div>
                                <span class="text-[10px] text-gray-400 font-bold sf-label">Verifikasi</span>
                            </div>
                            <div class="flex flex-col items-center gap-1" id="step-3">
                                <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 text-xs font-black flex items-center justify-center sf-dot">3</div>
                                <span class="text-[10px] text-gray-400 font-bold sf-label">Simpan</span>
                            </div>
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div id="progress-fill" class="h-full bg-[#C0271A] rounded-full transition-all duration-500" style="width:0%"></div>
                    </div>

                    {{-- Camera --}}
                    <div class="relative rounded-2xl overflow-hidden bg-gray-900 border-2 border-gray-200" style="aspect-ratio:4/3">
                        <video id="sf-video" autoplay muted playsinline class="w-full h-full object-cover block" style="transform:scaleX(-1)"></video>
                        <canvas id="sf-canvas" class="hidden"></canvas>

                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div id="cam-guide" class="w-40 h-52 rounded-full border-[2.5px] border-white/30 transition-all duration-300" style="box-shadow:0 0 0 9999px rgba(0,0,0,0.45)"></div>
                        </div>

                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2">
                            <span id="cam-badge" class="bg-black/60 backdrop-blur-sm text-white text-xs font-semibold px-4 py-1.5 rounded-full whitespace-nowrap">Memuat model...</span>
                        </div>

                        <div id="cam-loading" class="absolute inset-0 bg-black/75 flex flex-col items-center justify-center gap-3 text-sm text-gray-300">
                            <div class="w-10 h-10 border-[3px] border-[#C0271A]/30 border-t-[#C0271A] rounded-full animate-spin"></div>
                            <span>Memuat model pengenalan wajah...</span>
                        </div>
                    </div>

                    {{-- Capture slots --}}
                    <div id="captures" class="grid grid-cols-3 gap-3">
                        <div class="cap-slot aspect-square rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center text-2xl text-gray-300 overflow-hidden relative" id="slot-0">📷</div>
                        <div class="cap-slot aspect-square rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center text-2xl text-gray-300 overflow-hidden relative" id="slot-1">📷</div>
                        <div class="cap-slot aspect-square rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center text-2xl text-gray-300 overflow-hidden relative" id="slot-2">📷</div>
                    </div>

                    {{-- Hint --}}
                    <p id="hint-text" class="text-center text-xs text-gray-500 leading-relaxed">
                        Hadapkan wajah ke kamera, lalu klik tombol foto. Ambil <strong>3 foto</strong> dari sudut sedikit berbeda.
                    </p>

                    {{-- Capture button --}}
                    <button
                        id="btn-capture"
                        disabled
                        onclick="capturePhoto()"
                        class="w-full bg-[#C0271A] hover:bg-[#9B1E13] disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-black text-xl py-3.5 rounded-2xl transition-all shadow-lg shadow-red-200 flex items-center justify-center gap-2 active:scale-[.98]"
                        style="font-family:'Bebas Neue',sans-serif; letter-spacing:.05em"
                    >
                        📸 AMBIL FOTO (<span id="capture-count">0</span>/3)
                    </button>

                    {{-- Save button --}}
                    <button
                        id="btn-save"
                        onclick="saveToServer()"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-black text-xl py-3.5 rounded-2xl transition-all shadow-lg shadow-green-200 items-center justify-center gap-2 active:scale-[.98] hidden"
                        style="font-family:'Bebas Neue',sans-serif; letter-spacing:.05em"
                    >
                        ✅ SIMPAN DATA WAJAH
                    </button>

                    {{-- Reset --}}
                    <button
                        id="btn-reset"
                        onclick="resetCaptures()"
                        class="w-full border-2 border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-700 text-sm font-bold py-2.5 rounded-2xl transition hidden"
                    >
                        🔄 Ulangi dari awal
                    </button>

                    {{-- Status --}}
                    <div id="status-box" class="hidden rounded-2xl px-4 py-3 text-sm font-semibold text-center"></div>

                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">© 2026 Tahu Bakso Morojoyo · Kelompok 4</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const CSRF     = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const SAVE_URL = "{{ route('attendance.save-face') }}";
        const MODEL_URL = '/face-models';
        const NEEDED   = 3;

        let captures = [], faceDetected = false, lastDescriptor = null, detectionLoop = null;

        const video     = document.getElementById('sf-video');
        const canvas    = document.getElementById('sf-canvas');
        const guide     = document.getElementById('cam-guide');
        const badge     = document.getElementById('cam-badge');
        const loading   = document.getElementById('cam-loading');
        const btnCap    = document.getElementById('btn-capture');
        const btnSave   = document.getElementById('btn-save');
        const btnReset  = document.getElementById('btn-reset');
        const statusBox = document.getElementById('status-box');
        const countEl   = document.getElementById('capture-count');
        const hintEl    = document.getElementById('hint-text');
        const progress  = document.getElementById('progress-fill');

        const STATUS_CLASSES = {
            ok:   'bg-green-50 border border-green-200 text-green-700',
            err:  'bg-red-50 border border-red-200 text-red-700',
            wait: 'bg-blue-50 border border-blue-200 text-blue-700',
        };

        function showStatus(type, msg) {
            statusBox.className = 'rounded-2xl px-4 py-3 text-sm font-semibold text-center ' + (STATUS_CLASSES[type]||'');
            statusBox.innerHTML = msg;
            statusBox.classList.remove('hidden');
        }

        function updateStep(n) {
            for (let i = 1; i <= 3; i++) {
                const el  = document.getElementById('step-' + i);
                const dot = el.querySelector('.sf-dot');
                const lbl = el.querySelector('.sf-label');
                if (i < n) {
                    dot.className = 'w-7 h-7 rounded-full bg-green-500 text-white text-xs font-black flex items-center justify-center sf-dot';
                    dot.textContent = '✓';
                    lbl.className = 'text-[10px] text-green-600 font-bold sf-label';
                } else if (i === n) {
                    dot.className = 'w-7 h-7 rounded-full bg-[#C0271A] text-white text-xs font-black flex items-center justify-center shadow-md sf-dot';
                    dot.textContent = i;
                    lbl.className = 'text-[10px] text-[#C0271A] font-bold sf-label';
                } else {
                    dot.className = 'w-7 h-7 rounded-full bg-gray-200 text-gray-400 text-xs font-black flex items-center justify-center sf-dot';
                    dot.textContent = i;
                    lbl.className = 'text-[10px] text-gray-400 font-bold sf-label';
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
                const stream = await navigator.mediaDevices.getUserMedia({ video:{ facingMode:'user', width:640, height:480 } });
                video.srcObject = stream;
                await new Promise(r => video.addEventListener('loadedmetadata', r, { once:true }));
                canvas.width = video.videoWidth; canvas.height = video.videoHeight;
                loading.style.display = 'none';
                badge.textContent = 'Arahkan wajah ke kamera';
                startLoop();
            } catch(err) {
                loading.innerHTML = `<span class="text-red-300">⚠️ ${err.message || 'Gagal akses kamera'}</span>`;
            }
        }

        function startLoop() {
            detectionLoop = setInterval(async () => {
                try {
                    const det = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold:0.5 }))
                        .withFaceLandmarks(true).withFaceDescriptor();
                    if (det) {
                        faceDetected   = true;
                        lastDescriptor = Array.from(det.descriptor);
                        guide.style.borderColor = '#34d399';
                        guide.style.boxShadow   = '0 0 0 9999px rgba(0,0,0,0.45), 0 0 20px rgba(52,211,153,.3)';
                        badge.textContent = captures.length < NEEDED ? '✓ Wajah terdeteksi — siap foto' : '✓ Semua foto diambil';
                        if (captures.length < NEEDED) btnCap.disabled = false;
                    } else {
                        faceDetected   = false; lastDescriptor = null;
                        guide.style.borderColor = '#f87171';
                        guide.style.boxShadow   = '0 0 0 9999px rgba(0,0,0,0.45)';
                        badge.textContent = 'Wajah tidak terdeteksi...';
                        btnCap.disabled = true;
                    }
                } catch(_) {}
            }, 500);
        }

        function capturePhoto() {
            if (!faceDetected || captures.length >= NEEDED) return;
            const ctx = canvas.getContext('2d');
            ctx.save(); ctx.scale(-1,1); ctx.drawImage(video,-canvas.width,0,canvas.width,canvas.height); ctx.restore();
            const base64 = canvas.toDataURL('image/jpeg', 0.8);
            captures.push({ base64, descriptor:[...lastDescriptor] });
            const slot = document.getElementById('slot-' + (captures.length-1));
            slot.innerHTML = `<img src="${base64}" class="w-full h-full object-cover"><span class="absolute top-1.5 right-1.5 bg-[#C0271A] text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">${captures.length}</span>`;
            slot.classList.remove('border-dashed','border-gray-200','text-gray-300');
            slot.classList.add('border-green-400');
            countEl.textContent = captures.length;
            progress.style.width = ((captures.length / NEEDED) * 66) + '%';
            if (captures.length >= NEEDED) {
                clearInterval(detectionLoop);
                btnCap.disabled = true;
                btnSave.classList.remove('hidden'); btnSave.classList.add('flex');
                btnReset.classList.remove('hidden');
                hintEl.innerHTML = '3 foto berhasil diambil! Klik <strong class="text-[#C0271A]">Simpan Data Wajah</strong> untuk menyimpan.';
                updateStep(2);
                progress.style.width = '66%';
                showStatus('wait','⏳ Siap disimpan. Klik tombol simpan di bawah.');
            } else {
                const hints = ['Bagus! Sekarang miringkan kepala sedikit ke kiri.','Hampir selesai! Miringkan sedikit ke kanan.'];
                hintEl.textContent = hints[captures.length-1] || 'Ambil foto berikutnya.';
            }
        }

        async function saveToServer() {
            if (captures.length < NEEDED) return;
            btnSave.disabled = true;
            btnSave.innerHTML = '<div class="w-5 h-5 border-2 border-white/40 border-t-white rounded-full animate-spin mr-2"></div> Menyimpan...';
            showStatus('wait','⏳ Mengirim data wajah ke server...');
            updateStep(3);
            const avgDescriptor = captures[0].descriptor.map((_,i) =>
                captures.reduce((sum,c) => sum + c.descriptor[i], 0) / captures.length
            );
            try {
                const res  = await fetch(SAVE_URL, {
                    method:'POST',
                    headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json' },
                    body: JSON.stringify({ face_descriptor:avgDescriptor, foto_base64:captures[0].base64 }),
                });
                const data = await res.json();
                if (data.success) {
                    progress.style.width = '100%';
                    showStatus('ok','✅ Data wajah berhasil disimpan! Kamu sekarang bisa absen.');
                    btnSave.classList.add('hidden'); btnReset.classList.add('hidden');
                    setTimeout(() => { window.location.href = '/absensi'; }, 2500);
                } else {
                    showStatus('err','❌ ' + (data.message || 'Gagal menyimpan.'));
                    btnSave.disabled = false;
                    btnSave.innerHTML = '✅ SIMPAN DATA WAJAH';
                }
            } catch(err) {
                showStatus('err','❌ Koneksi bermasalah. Coba lagi.');
                btnSave.disabled = false;
                btnSave.innerHTML = '✅ SIMPAN DATA WAJAH';
            }
        }

        function resetCaptures() {
            captures = [];
            for (let i = 0; i < NEEDED; i++) {
                const slot = document.getElementById('slot-' + i);
                slot.innerHTML = '📷';
                slot.className = 'cap-slot aspect-square rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center text-2xl text-gray-300 overflow-hidden relative';
            }
            countEl.textContent = '0';
            btnCap.disabled = !faceDetected;
            btnSave.classList.add('hidden'); btnSave.classList.remove('flex');
            btnReset.classList.add('hidden');
            statusBox.classList.add('hidden');
            hintEl.innerHTML = 'Hadapkan wajah ke kamera, lalu klik tombol foto. Ambil <strong>3 foto</strong> dari sudut sedikit berbeda.';
            updateStep(1);
            progress.style.width = '0%';
            startLoop();
        }

        initFaceApi();
    </script>

@endsection