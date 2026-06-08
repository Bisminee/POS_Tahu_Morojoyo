{{-- resources/views/owner/karyawan-face.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftarkan Face ID — {{ $karyawan->nama }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background: #f4f5f7;
            margin: 0;
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            max-width: 480px;
            margin: 0 auto;
            border: 1px solid #e9eaec;
        }

        h2 {
            font-size: 18px;
            margin: 0 0 4px;
        }

        p {
            font-size: 13px;
            color: #6b7280;
            margin: 0 0 20px;
        }

        .camera-wrap {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            background: #111;
            aspect-ratio: 4/3;
            margin-bottom: 12px;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        canvas {
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

        .cam-status {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, .6);
            color: #fff;
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 99px;
        }

        .loading-overlay {
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
        }

        .spinner {
            width: 32px;
            height: 32px;
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

        .btn {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: none;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-green {
            background: #059669;
            color: #fff;
        }

        .btn-green:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .btn-gray {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .status {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 10px;
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

        .back-link {
            display: block;
            text-align: center;
            margin-top: 12px;
            font-size: 13px;
            color: #6b7280;
            text-decoration: none;
        }

        .back-link:hover {
            color: #374151;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Daftarkan Face ID</h2>
        <p>Karyawan: <strong>{{ $karyawan->nama }}</strong></p>

        <div class="camera-wrap">
            <video id="video" autoplay muted playsinline></video>
            <canvas id="canvas"></canvas>
            <div class="face-overlay">
                <div class="face-guide" id="face-guide"></div>
            </div>
            <div class="cam-status" id="cam-status">Memuat model...</div>
            <div class="loading-overlay" id="loading-overlay">
                <div class="spinner"></div>
                <span>Memuat model wajah...</span>
            </div>
        </div>

        <button class="btn btn-green" id="btn-save" disabled onclick="saveFace()">
            📸 Ambil & Simpan Face ID
        </button>

        <div class="status" id="status-box"></div>

        <a href="{{ url()->previous() }}" class="back-link">← Kembali</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const SAVE_URL = "{{ route('owner.karyawan.save-face', $karyawan->idKaryawan) }}";
        const CSRF = "{{ csrf_token() }}";
        const MODEL_URL = '/face-models';

        let lastDescriptor = null;
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const guide = document.getElementById('face-guide');
        const camStatus = document.getElementById('cam-status');
        const loading = document.getElementById('loading-overlay');
        const btn = document.getElementById('btn-save');
        const statusBox = document.getElementById('status-box');

        async function init() {
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
            camStatus.textContent = 'Arahkan wajah ke kamera';
            startLoop();
        }

        function startLoop() {
            setInterval(async () => {
                const det = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                        scoreThreshold: 0.5
                    }))
                    .withFaceLandmarks(true)
                    .withFaceDescriptor();
                if (det) {
                    lastDescriptor = Array.from(det.descriptor);
                    guide.className = 'face-guide detected';
                    camStatus.textContent = 'Wajah terdeteksi — siap disimpan';
                    btn.disabled = false;
                } else {
                    lastDescriptor = null;
                    guide.className = 'face-guide';
                    camStatus.textContent = 'Arahkan wajah ke kamera...';
                    btn.disabled = true;
                }
            }, 600);
        }

        function capturePhoto() {
            const ctx = canvas.getContext('2d');
            ctx.save();
            ctx.scale(-1, 1);
            ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
            ctx.restore();
            return canvas.toDataURL('image/jpeg', 0.85);
        }

        async function saveFace() {
            if (!lastDescriptor) return;
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';

            try {
                const res = await fetch(SAVE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        face_descriptor: lastDescriptor,
                        foto_base64: capturePhoto(),
                    }),
                });
                const data = await res.json();
                statusBox.className = 'status ' + (data.success ? 'status-ok' : 'status-err');
                statusBox.textContent = data.message;
                statusBox.style.display = '';
                if (data.success) btn.textContent = 'Tersimpan';
                else {
                    btn.disabled = false;
                    btn.textContent = 'Ambil & Simpan Face ID';
                }
            } catch (e) {
                statusBox.className = 'status status-err';
                statusBox.textContent = 'Gagal koneksi. Coba lagi.';
                statusBox.style.display = '';
                btn.disabled = false;
                btn.textContent = 'Ambil & Simpan Face ID';
            }
        }

        init();
    </script>
</body>

</html>
