@extends('layouts.app')

@section('title', 'Setup Wajah')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap');

        .sf-body {
            font-family: 'DM Sans', sans-serif;
            background: #0f1117;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            margin: 0;
        }

        .sf-card {
            background: #1a1d27;
            border: 1px solid #2a2d3a;
            border-radius: 28px;
            padding: 36px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 32px 80px rgba(0, 0, 0, .5);
        }

        .sf-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .sf-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 16px;
        }

        .sf-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #f1f5f9;
            margin: 0 0 6px;
        }

        .sf-header p {
            font-size: 13px;
            color: #64748b;
            margin: 0;
            line-height: 1.5;
        }

        .sf-steps {
            display: flex;
            margin-bottom: 24px;
            position: relative;
        }

        .sf-steps::before {
            content: '';
            position: absolute;
            top: 14px;
            left: 14px;
            right: 14px;
            height: 2px;
            background: #2a2d3a;
            z-index: 0;
        }

        .sf-step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .sf-step-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #2a2d3a;
            border: 2px solid #3a3d4a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            margin: 0 auto 6px;
            transition: all .3s;
        }

        .sf-step.active .sf-step-dot {
            background: #6366f1;
            border-color: #6366f1;
            color: #fff;
            box-shadow: 0 0 16px rgba(99, 102, 241, .4);
        }

        .sf-step.done .sf-step-dot {
            background: #10b981;
            border-color: #10b981;
            color: #fff;
        }

        .sf-step-label {
            font-size: 10px;
            color: #64748b;
            font-weight: 500;
        }

        .sf-step.active .sf-step-label {
            color: #a5b4fc;
        }

        .cam-wrap {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: #0a0b0f;
            aspect-ratio: 4/3;
            margin-bottom: 16px;
            border: 2px solid #2a2d3a;
        }

        #sf-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transform: scaleX(-1);
        }

        #sf-canvas {
            display: none;
        }

        .cam-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .cam-guide {
            width: 160px;
            height: 200px;
            border-radius: 50%;
            border: 2.5px solid rgba(255, 255, 255, .25);
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, .45);
            transition: border-color .3s;
            position: relative;
        }

        .cam-guide.detected {
            border-color: #34d399;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, .45), 0 0 24px rgba(52, 211, 153, .3);
        }

        .cam-guide.no-face {
            border-color: #f87171;
        }

        .cam-badge {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, .7);
            backdrop-filter: blur(8px);
            color: #fff;
            font-size: 11px;
            font-weight: 500;
            padding: 5px 14px;
            border-radius: 99px;
            white-space: nowrap;
            font-family: 'DM Sans', sans-serif;
        }

        .cam-loading {
            position: absolute;
            inset: 0;
            background: rgba(10, 11, 15, .85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: #94a3b8;
            font-size: 13px;
        }

        .ring {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(99, 102, 241, .2);
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin .9s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .captures {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .cap-slot {
            flex: 1;
            aspect-ratio: 1;
            border-radius: 12px;
            background: #0f1117;
            border: 2px dashed #2a2d3a;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3a3d4a;
            font-size: 20px;
            transition: all .3s;
            position: relative;
        }

        .cap-slot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cap-slot.filled {
            border-color: #10b981;
            border-style: solid;
        }

        .cap-slot .cap-num {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(0, 0, 0, .6);
            color: #10b981;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 5px;
            border-radius: 4px;
        }

        .hint {
            font-size: 12px;
            color: #475569;
            text-align: center;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .btn-capture {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            border: none;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: opacity .15s, transform .1s;
            margin-bottom: 10px;
        }

        .btn-capture:hover:not(:disabled) {
            opacity: .9;
        }

        .btn-capture:active:not(:disabled) {
            transform: scale(.98);
        }

        .btn-capture:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        .btn-save {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            border: none;
            background: #10b981;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: background .15s, transform .1s;
            display: none;
        }

        .btn-save:hover:not(:disabled) {
            background: #059669;
        }

        .btn-save:active:not(:disabled) {
            transform: scale(.98);
        }

        .btn-save:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn-reset {
            width: 100%;
            padding: 10px;
            border-radius: 12px;
            border: 1px solid #2a2d3a;
            background: transparent;
            color: #64748b;
            font-size: 13px;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            margin-top: 8px;
            display: none;
            transition: color .15s, border-color .15s;
        }

        .btn-reset:hover {
            color: #94a3b8;
            border-color: #3a3d4a;
        }

        .status-box {
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 12px;
            display: none;
            text-align: center;
        }

        .s-ok {
            background: #064e3b;
            color: #6ee7b7;
            border: 1px solid #065f46;
        }

        .s-err {
            background: #450a0a;
            color: #fca5a5;
            border: 1px solid #7f1d1d;
        }

        .s-wait {
            background: #1e1b4b;
            color: #a5b4fc;
            border: 1px solid #312e81;
        }

        .progress-bar {
            height: 4px;
            background: #2a2d3a;
            border-radius: 99px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            border-radius: 99px;
            transition: width .4s ease;
            width: 0%;
        }
    </style>

    <div class="sf-body">
        <div class="sf-card">
            <div class="sf-header">
                <div class="sf-icon">🪪</div>
                <h1>Setup Wajah</h1>
                <p>Foto wajah diambil 3x dari sudut berbeda<br>untuk akurasi pengenalan yang lebih baik</p>
            </div>

            <div class="sf-steps">
                <div class="sf-step active" id="step-1">
                    <div class="sf-step-dot">1</div>
                    <div class="sf-step-label">Foto</div>
                </div>
                <div class="sf-step" id="step-2">
                    <div class="sf-step-dot">2</div>
                    <div class="sf-step-label">Verifikasi</div>
                </div>
                <div class="sf-step" id="step-3">
                    <div class="sf-step-dot">3</div>
                    <div class="sf-step-label">Simpan</div>
                </div>
            </div>

            <div class="progress-bar">
                <div class="progress-fill" id="progress-fill"></div>
            </div>

            <div class="cam-wrap">
                <video id="sf-video" autoplay muted playsinline></video>
                <canvas id="sf-canvas"></canvas>
                <div class="cam-overlay">
                    <div class="cam-guide" id="cam-guide"></div>
                </div>
                <div class="cam-badge" id="cam-badge">Memuat model...</div>
                <div class="cam-loading" id="cam-loading">
                    <div class="ring"></div>
                    <span>Memuat model pengenalan wajah...</span>
                </div>
            </div>

            <div class="captures" id="captures">
                <div class="cap-slot" id="slot-0">📷</div>
                <div class="cap-slot" id="slot-1">📷</div>
                <div class="cap-slot" id="slot-2">📷</div>
            </div>

            <p class="hint" id="hint-text">Hadapkan wajah ke kamera, lalu klik tombol foto. Ambil 3 foto dari sudut
                sedikit berbeda.</p>

            <button class="btn-capture" id="btn-capture" disabled onclick="capturePhoto()">
                📸 Ambil Foto (<span id="capture-count">0</span>/3)
            </button>

            <button class="btn-save" id="btn-save" onclick="saveToServer()">
                ✅ Simpan Data Wajah
            </button>

            <button class="btn-reset" id="btn-reset" onclick="resetCaptures()">
                🔄 Ulangi dari awal
            </button>

            <div class="status-box" id="status-box"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const SAVE_URL = "{{ route('attendance.save-face') }}";
        const MODEL_URL = '/face-models';
        const NEEDED = 3;

        let captures = [],
            faceDetected = false,
            lastDescriptor = null,
            detectionLoop = null;

        const video = document.getElementById('sf-video');
        const canvas = document.getElementById('sf-canvas');
        const guide = document.getElementById('cam-guide');
        const badge = document.getElementById('cam-badge');
        const loading = document.getElementById('cam-loading');
        const btnCap = document.getElementById('btn-capture');
        const btnSave = document.getElementById('btn-save');
        const btnReset = document.getElementById('btn-reset');
        const statusBox = document.getElementById('status-box');
        const countEl = document.getElementById('capture-count');
        const hintEl = document.getElementById('hint-text');
        const progress = document.getElementById('progress-fill');

        function showStatus(type, msg) {
            statusBox.className = 'status-box s-' + type;
            statusBox.innerHTML = msg;
            statusBox.style.display = '';
        }

        function updateStep(n) {
            for (let i = 1; i <= 3; i++) {
                const el = document.getElementById('step-' + i);
                el.className = 'sf-step' + (i < n ? ' done' : i === n ? ' active' : '');
                el.querySelector('.sf-step-dot').textContent = i < n ? '✓' : i;
            }
        }

        async function initFaceApi() {
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                ]);
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: 640,
                        height: 480
                    }
                });
                video.srcObject = stream;
                await new Promise(r => video.addEventListener('loadedmetadata', r, {
                    once: true
                }));
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                loading.style.display = 'none';
                badge.textContent = 'Arahkan wajah ke kamera';
                startLoop();
            } catch (err) {
                loading.innerHTML = `<span style="color:#fca5a5">⚠️ ${err.message || 'Gagal akses kamera'}</span>`;
            }
        }

        function startLoop() {
            detectionLoop = setInterval(async () => {
                try {
                    const det = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                            scoreThreshold: 0.5
                        }))
                        .withFaceLandmarks(true)
                        .withFaceDescriptor();
                    if (det) {
                        faceDetected = true;
                        lastDescriptor = Array.from(det.descriptor);
                        guide.className = 'cam-guide detected';
                        badge.textContent = captures.length < NEEDED ? '✓ Wajah terdeteksi — siap foto' :
                            '✓ Semua foto diambil';
                        if (captures.length < NEEDED) btnCap.disabled = false;
                    } else {
                        faceDetected = false;
                        lastDescriptor = null;
                        guide.className = 'cam-guide no-face';
                        badge.textContent = 'Wajah tidak terdeteksi...';
                        btnCap.disabled = true;
                    }
                } catch (_) {}
            }, 500);
        }

        function capturePhoto() {
            if (!faceDetected || captures.length >= NEEDED) return;
            const ctx = canvas.getContext('2d');
            ctx.save();
            ctx.scale(-1, 1);
            ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
            ctx.restore();
            const base64 = canvas.toDataURL('image/jpeg', 0.8);
            captures.push({
                base64,
                descriptor: [...lastDescriptor]
            });
            const slot = document.getElementById('slot-' + (captures.length - 1));
            slot.innerHTML = `<img src="${base64}"><span class="cap-num">${captures.length}</span>`;
            slot.classList.add('filled');
            countEl.textContent = captures.length;
            progress.style.width = ((captures.length / NEEDED) * 66) + '%';
            if (captures.length >= NEEDED) {
                clearInterval(detectionLoop);
                btnCap.disabled = true;
                btnSave.style.display = 'block';
                btnReset.style.display = 'block';
                hintEl.textContent = '3 foto berhasil diambil! Klik "Simpan Data Wajah" untuk menyimpan.';
                updateStep(2);
                progress.style.width = '66%';
                showStatus('wait', '⏳ Siap disimpan. Klik tombol simpan di bawah.');
            } else {
                const hints = ['Bagus! Sekarang miringkan kepala sedikit ke kiri.',
                    'Hampir selesai! Miringkan sedikit ke kanan.'
                ];
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
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        face_descriptor: avgDescriptor,
                        foto_base64: captures[0].base64
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    progress.style.width = '100%';
                    showStatus('ok', '✅ Data wajah berhasil disimpan! Kamu sekarang bisa absen.');
                    btnSave.style.display = 'none';
                    btnReset.style.display = 'none';
                    setTimeout(() => {
                        window.location.href = '/absensi';
                    }, 2500);
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
                slot.classList.remove('filled');
            }
            countEl.textContent = '0';
            btnCap.disabled = !faceDetected;
            btnSave.style.display = 'none';
            btnReset.style.display = 'none';
            statusBox.style.display = 'none';
            hintEl.textContent =
            'Hadapkan wajah ke kamera, lalu klik tombol foto. Ambil 3 foto dari sudut sedikit berbeda.';
            updateStep(1);
            progress.style.width = '0%';
            startLoop();
        }

        initFaceApi();
    </script>
@endsection
