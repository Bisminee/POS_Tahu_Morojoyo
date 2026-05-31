{{-- resources/views/attendance/index.blade.php --}}
@extends('layouts.app') {{-- sesuaikan dengan layout yang dipakai --}}

@section('title', 'Absensi Masuk')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f5f7;
            margin: 0;
        }

        .abs-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .abs-card {
            background: #fff;
            border-radius: 24px;
            padding: 32px;
            width: 100%;
            max-width: 480px;
            border: 1px solid #e9eaec;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .08);
        }

        .abs-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .abs-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 4px;
        }

        .abs-header p {
            font-size: 13px;
            color: #6b7280;
        }

        .shift-info {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #166534;
        }

        .shift-info .si-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .shift-info .si-row span:first-child {
            color: #6b7280;
        }

        .shift-info .si-row strong {
            font-weight: 700;
        }

        .no-shift {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            color: #991b1b;
            font-size: 13px;
            margin-bottom: 20px;
        }

        /* Kamera */
        .camera-wrap {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background: #111;
            margin-bottom: 16px;
            aspect-ratio: 4/3;
        }

        #video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transform: scaleX(-1);
            /* mirror */
        }

        #canvas {
            display: none;
        }

        .face-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .face-guide {
            width: 180px;
            height: 220px;
            border: 2.5px solid rgba(255, 255, 255, .5);
            border-radius: 50%;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, .35);
        }

        .face-guide.detected {
            border-color: #34d399;
        }

        .face-guide.no-face {
            border-color: #f87171;
        }

        .camera-status {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, .6);
            color: #fff;
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 99px;
            white-space: nowrap;
        }

        .loading-models {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            gap: 10px;
            border-radius: 16px;
        }

        .spinner-lg {
            width: 36px;
            height: 36px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-absen {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            border: none;
            background: #059669;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background .15s, transform .1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-absen:hover:not(:disabled) {
            background: #047857;
        }

        .btn-absen:active:not(:disabled) {
            transform: scale(.98);
        }

        .btn-absen:disabled {
            background: #9ca3af;
            cursor: not-allowed;
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

        .status-ok {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-err {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .status-warn {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .status-wait {
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .spinner-sm {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, .4);
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            vertical-align: middle;
        }
    </style>

    <div class="abs-wrap">
        <div class="abs-card">
            <div class="abs-header">
                <h1>Absensi Masuk</h1>
                <p>{{ now()->translatedFormat('l, d F Y') }} &middot; {{ now()->format('H:i') }}</p>
            </div>

            @if (session('status'))
                <div class="status-box status-ok" style="margin-bottom:16px; display:block;">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="status-box status-err" style="display:block;margin-bottom:16px">
                    {{ session('error') }}
                </div>
            @endif

            @if (!empty($cabangName))
                <div class="status-box" style="background:#eff6ff;color:#0c4a6e;margin-bottom:16px;border:1px solid #bfdbfe;">
                    Pilih shift untuk cabang <strong>{{ $cabangName }}</strong>. Jika masih jam siang, daftar shift siang akan otomatis tampil.
                </div>
            @endif

            {{-- KONDISI 1: Sudah pilih shift → tampilkan info shift --}}
            @if ($shift)
                <div class="shift-info">
                    <div class="si-row">
                        <span>Karyawan</span>
                        <strong>{{ $shift->karyawan->nama }}</strong>
                    </div>
                    <div class="si-row">
                        <span>Sesi</span>
                        <strong>{{ ucfirst($shift->sesi) }}</strong>
                    </div>
                    <div class="si-row">
                        <span>Jam masuk</span>
                        <strong>{{ substr($shift->jam_mulai, 0, 5) }}</strong>
                    </div>
                    <div class="si-row">
                        <span>Jam selesai</span>
                        <strong>{{ substr($shift->jam_selesai, 0, 5) }}</strong>
                    </div>
                    <div class="si-row">
                        <span>Toleransi</span>
                        <strong>{{ $shift->toleransi_menit }} menit</strong>
                    </div>
                </div>

                {{-- KONDISI 2: Belum pilih shift, tapi ada shift hari ini → dropdown pilih --}}
            @elseif ($todayShifts->isNotEmpty())
                <form method="POST" action="{{ route('attendance.select-shift') }}" style="margin-bottom:20px">
                    @csrf

                    @if ($errors->any())
                        <div class="no-shift" style="background:#fef2f2;border-color:#fecaca;color:#991b1b;">
                            Terjadi kesalahan: {{ $errors->first() }}
                        </div>
                    @endif

                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px">
                        Pilih karyawan yang jaga sekarang:
                    </label>
                    <select name="shift_id"
                        style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid #d1d5db;font-size:14px;font-family:inherit;margin-bottom:10px">
                        @foreach ($todayShifts as $s)
                            <option value="{{ $s->id }}">
                                {{ $s->karyawan->nama }} — Sesi {{ ucfirst($s->sesi) }}
                                ({{ substr($s->jam_mulai, 0, 5) }}–{{ substr($s->jam_selesai, 0, 5) }})
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-absen" style="background:#3b82f6">
                        ✓ Pilih &amp; Lanjut ke Absensi
                    </button>
                </form>

                {{-- KONDISI 3: Tidak ada shift sama sekali hari ini --}}
            @else
                <div class="no-shift">
                    Belum ada jadwal shift untuk hari ini.<br>
                    <span style="font-size:12px">Hubungi owner untuk membuat jadwal.</span>
                </div>
            @endif

            {{-- Kamera & tombol — hanya tampil jika shift sudah dipilih --}}
            @if ($shift)
                <div class="camera-wrap" id="camera-wrap">
                    <video id="video" autoplay muted playsinline></video>
                    <canvas id="canvas"></canvas>
                    <div class="face-overlay">
                        <div class="face-guide" id="face-guide"></div>
                    </div>
                    <div class="camera-status" id="camera-status">Memuat model...</div>
                    <div class="loading-models" id="loading-models">
                        <div class="spinner-lg"></div>
                        <span>Memuat model pengenalan wajah...</span>
                    </div>
                </div>

                <button class="btn-absen" id="btn-absen" disabled onclick="doAbsen()">
                    📸 Absen Sekarang
                </button>

                <div class="status-box" id="status-box"></div>
            @endif
        </div>
    </div>

    {{-- face-api.js dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const CLOCK_IN_URL = "{{ route('attendance.clock-in') }}";
        const POS_URL = "{{ route('cashier.pos') }}";
        const HAS_SHIFT = {{ $shift ? 'true' : 'false' }};
        const MODEL_URL = '/face-models'; // taruh model di public/face-models/

        let faceDetected = false;
        let lastDescriptor = null;
        let detectionLoop = null;

        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const guide = document.getElementById('face-guide');
        const camStatus = document.getElementById('camera-status');
        const loadingEl = document.getElementById('loading-models');
        const btnAbsen = document.getElementById('btn-absen');
        const statusBox = document.getElementById('status-box');

        function showStatus(type, msg) {
            statusBox.className = 'status-box status-' + type;
            statusBox.innerHTML = msg;
            statusBox.style.display = '';
        }

        async function initFaceApi() {
            try {
                // Load model yang diperlukan
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                ]);

                // Akses kamera
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

                loadingEl.style.display = 'none';
                camStatus.textContent = 'Arahkan wajah ke kamera';

                if (HAS_SHIFT) startDetectionLoop();

            } catch (err) {
                loadingEl.innerHTML = `<span style="color:#fca5a5">⚠️ ${err.message || 'Gagal akses kamera'}</span>`;
            }
        }

        function startDetectionLoop() {
            detectionLoop = setInterval(async () => {
                try {
                    const detection = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                            scoreThreshold: 0.5
                        }))
                        .withFaceLandmarks(true)
                        .withFaceDescriptor();

                    if (detection) {
                        faceDetected = true;
                        lastDescriptor = Array.from(detection.descriptor);
                        guide.className = 'face-guide detected';
                        camStatus.textContent = '✓ Wajah terdeteksi';
                        btnAbsen.disabled = false;
                    } else {
                        faceDetected = false;
                        lastDescriptor = null;
                        guide.className = 'face-guide no-face';
                        camStatus.textContent = 'Wajah tidak terdeteksi...';
                        btnAbsen.disabled = true;
                    }
                } catch (_) {}
            }, 600);
        }

        function capturePhoto() {
            const ctx = canvas.getContext('2d');
            ctx.save();
            ctx.scale(-1, 1);
            ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
            ctx.restore();
            return canvas.toDataURL('image/jpeg', 0.8);
        }

        async function doAbsen() {
            if (!faceDetected || !lastDescriptor) {
                showStatus('warn', 'Pastikan wajah terdeteksi dulu di kamera.');
                return;
            }

            clearInterval(detectionLoop);
            btnAbsen.disabled = true;
            btnAbsen.innerHTML = '<span class="spinner-sm"></span> Memverifikasi wajah...';
            showStatus('wait', '⏳ Memverifikasi wajah...');

            const fotoBase64 = capturePhoto();

            try {
                const res = await fetch(CLOCK_IN_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        face_descriptor: lastDescriptor,
                        foto_base64: fotoBase64,
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    if (data.status_masuk === 'telat') {
                        showStatus('warn', `⚠️ ${data.message}`);
                    } else {
                        showStatus('ok', `✅ ${data.message}`);
                    }
                    // Redirect ke POS langsung
                    setTimeout(() => {
                        window.location.href = data.redirect || POS_URL;
                    }, 900);
                } else {
                    showStatus('err', `❌ ${data.message}`);
                    btnAbsen.disabled = false;
                    btnAbsen.innerHTML = '📸 Absen Sekarang';
                    startDetectionLoop();
                }
            } catch (err) {
                showStatus('err', '❌ Terjadi kesalahan koneksi. Coba lagi.');
                btnAbsen.disabled = false;
                btnAbsen.innerHTML = '📸 Absen Sekarang';
                startDetectionLoop();
            }
        }

        // Init saat halaman dimuat
        if (HAS_SHIFT) {
            initFaceApi();
        }
    </script>
@endsection
