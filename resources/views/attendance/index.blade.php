@props(['title' => 'Absensi Kasir'])

@php
    /*
     * Controller multi-karyawan mengirim $activeAttendances.
     * Fallback ini disediakan agar tidak error jika controller lama masih mengirim $activeAttendance.
     */
    $activeAttendances = $activeAttendances ?? collect(isset($activeAttendance) && $activeAttendance ? [$activeAttendance] : []);
@endphp

<x-layouts.app :title="$title">
    <style>
        body {
            background: #f4f5f7;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .attendance-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .attendance-card {
            width: 100%;
            max-width: 860px;
            background: #ffffff;
            border-radius: 28px;
            padding: 34px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .12);
            border: 1px solid #e5e7eb;
        }

        .attendance-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .attendance-header h1 {
            font-size: 32px;
            margin: 0;
            color: #071331;
            font-weight: 800;
        }

        .attendance-header p {
            margin: 8px 0 0;
            color: #64748b;
            font-size: 15px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #047857;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .section-title {
            font-size: 20px;
            font-weight: 900;
            color: #071331;
            margin: 24px 0 12px;
        }

        .active-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 18px;
        }

        .active-box h2 {
            margin: 0 0 8px;
            font-size: 18px;
            color: #166534;
        }

        .active-box p {
            margin: 4px 0;
            color: #166534;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        select,
        textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 15px;
            outline: none;
            background: #f8fafc;
        }

        select:focus,
        textarea:focus {
            border-color: #4f46e5;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, .12);
        }

        .btn {
            width: 100%;
            border: none;
            border-radius: 16px;
            padding: 15px 18px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: .2s;
            text-align: center;
        }

        .btn:disabled {
            background: #9ca3af !important;
            cursor: not-allowed;
        }

        .btn-primary {
            background: #4f46e5;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-danger {
            background: #dc2626;
            color: #ffffff;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            width: 100%;
            border-radius: 16px;
            padding: 15px 18px;
            font-size: 15px;
            font-weight: 800;
            background: #f3f4f6;
            color: #374151;
            margin-top: 10px;
            border: none;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .empty-box {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            font-size: 14px;
        }

        .camera-box {
            margin: 18px 0;
            padding: 16px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            background: #111827;
            margin-bottom: 12px;
        }

        .video-wrapper video {
            width: 100%;
            border-radius: 16px;
            background: #111827;
            display: none;
            margin-bottom: 0;
        }

        .video-wrapper video.active {
            display: block;
        }

        .video-wrapper canvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: none;
            pointer-events: none;
        }

        .video-wrapper canvas.active {
            display: block;
        }

        .face-status {
            padding: 12px 14px;
            border-radius: 14px;
            background: #eef2ff;
            color: #3730a3;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .button-gap {
            display: grid;
            gap: 10px;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 24px 0;
        }
    </style>

    {{--
        Face descriptor karyawan yang sedang shift — dikirim dari controller.
        Format: JSON string dari array Float32 (128 angka).
        Contoh di controller:
            $shift->karyawan->face_descriptor  (sudah json_encode saat disimpan)
    --}}
    @php
        $faceDescriptorRaw = $shift?->karyawan?->face_descriptor ?? null;
    @endphp

    <pre>
shift: {{ $shift?->id }}
karyawan: {{ $shift?->karyawan?->nama }}
face_descriptor: {{ $faceDescriptorRaw ? 'ADA' : 'NULL' }}
cabang_id shift: {{ $shift?->cabang_id }}
cabang_id user: {{ auth()->user()->cabang_id }}
</pre>

    <div class="abs-wrap">
        <div class="abs-card">

            <div class="abs-header">
                <h1>Absensi Masuk</h1>
                <p>{{ now()->translatedFormat('l, d F Y') }} &middot; {{ now()->format('H:i') }}</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if ($activeAttendances->count())
                <div class="section-title">Karyawan Sedang Shift</div>

                @foreach ($activeAttendances as $activeAttendance)
                    <div class="active-box">
                        <h2>{{ $activeAttendance->karyawan?->nama ?? '-' }}</h2>
                        <p><strong>Jam Masuk:</strong> {{ $activeAttendance->jam_masuk?->format('H:i') ?? '-' }}</p>

                        <form method="POST" action="{{ route('attendance.clock-out') }}" id="clockOutForm_{{ $activeAttendance->id }}" style="margin-top: 12px;">
                            @csrf

                            <input type="hidden" name="attendance_id" value="{{ $activeAttendance->id }}">
                            <input type="hidden" name="face_descriptor" id="face_descriptor_out_{{ $activeAttendance->id }}">
                            <input type="hidden" name="foto_base64" id="foto_base64_out_{{ $activeAttendance->id }}">

                            <div class="camera-box">
                                <div class="face-status" id="faceStatusOut_{{ $activeAttendance->id }}">
                                    Untuk absen pulang {{ $activeAttendance->karyawan?->nama ?? '-' }}, scan wajah terlebih dahulu.
                                </div>

                                <div class="video-wrapper">
                                    <video id="videoOut_{{ $activeAttendance->id }}" autoplay muted playsinline></video>
                                    <canvas id="canvasOut_{{ $activeAttendance->id }}"></canvas>
                                </div>

                                <div class="button-gap">
                                    <button type="button" class="btn-secondary"
                                        onclick="scanFace('out', {{ $activeAttendance->id }})">
                                        Scan Wajah Pulang {{ $activeAttendance->karyawan?->nama ?? '' }}
                                    </button>

                                    <button type="submit" class="btn btn-danger" id="clockOutSubmit_{{ $activeAttendance->id }}" disabled>
                                        Selesai Shift / Absen Pulang
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach

                <a href="{{ route('cashier.pos') }}" class="btn-secondary">
                    Masuk ke POS
                </a>

                <div class="divider"></div>
            @endif

            <div class="section-title">Tambah Karyawan Shift</div>

            @if ($karyawans->count())
                <form method="POST" action="{{ route('attendance.clock-in') }}" id="clockInForm">
                    @csrf

                    <div class="form-group">
                        <label for="karyawan_id">Pilih Nama Karyawan</label>
                        <select name="karyawan_id" id="karyawan_id" required>
                            <option value="">-- Pilih karyawan yang mulai shift --</option>
                            @foreach ($karyawans as $karyawan)
                                <option value="{{ $karyawan->idKaryawan }}">
                                    {{ $karyawan->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="face_descriptor" id="face_descriptor_in">
                    <input type="hidden" name="foto_base64" id="foto_base64_in">

                    <div class="camera-box">
                        <div class="face-status" id="faceStatusIn">
                            Pilih nama karyawan, lalu scan wajah sebelum absen masuk.
                        </div>

                        <div class="video-wrapper">
                            <video id="videoIn" autoplay muted playsinline></video>
                            <canvas id="canvasIn"></canvas>
                        </div>

                        <div class="button-gap">
                            <button type="button" class="btn-secondary"
                                onclick="scanFace('in')">
                                Scan Wajah Masuk
                            </button>

                            <button type="submit" class="btn btn-primary" id="clockInSubmit" disabled>
                                Mulai Shift / Absen Masuk
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="empty-box">
                    Semua karyawan aktif sudah berada dalam shift, atau belum ada data karyawan aktif.
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <script>
        let faceModelsLoaded = false;
        let currentStream = null;

        const MODEL_URL = '/face-models';

        function setFaceStatus(type, message, attendanceId = null) {
            let statusId;

            if (type === 'in') {
                statusId = 'faceStatusIn';
            } else {
                statusId = 'faceStatusOut_' + attendanceId;
            }

            const statusElement = document.getElementById(statusId);

            if (statusElement) {
                statusElement.innerText = message;
            }

            console.log('[Absensi Face ID]', message);
        }

        async function loadFaceModels() {
            if (faceModelsLoaded) {
                return true;
            }

            if (typeof faceapi === 'undefined') {
                alert('Library face-api.js belum berhasil dimuat. Pastikan koneksi internet aktif.');
                return false;
            }

            try {
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);

                faceModelsLoaded = true;
                return true;
            } catch (error) {
                console.error('Load Face API model error:', error);
                alert('Gagal memuat model Face API. Pastikan folder public/face-models sudah benar.');
                return false;
            }
        }

        async function stopCamera() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }
        }

        async function scanFace(type, attendanceId = null) {
            const isClockIn = type === 'in';

            const videoId = isClockIn ? 'videoIn' : 'videoOut_' + attendanceId;
            const canvasId = isClockIn ? 'canvasIn' : 'canvasOut_' + attendanceId;
            const descriptorInputId = isClockIn ? 'face_descriptor_in' : 'face_descriptor_out_' + attendanceId;
            const fotoInputId = isClockIn ? 'foto_base64_in' : 'foto_base64_out_' + attendanceId;
            const submitButtonId = isClockIn ? 'clockInSubmit' : 'clockOutSubmit_' + attendanceId;

            const video = document.getElementById(videoId);
            const overlayCanvas = document.getElementById(canvasId);
            const descriptorInput = document.getElementById(descriptorInputId);
            const fotoInput = document.getElementById(fotoInputId);
            const submitButton = document.getElementById(submitButtonId);

            if (isClockIn) {
                const karyawanSelect = document.getElementById('karyawan_id');

                if (!karyawanSelect || !karyawanSelect.value) {
                    alert('Pilih nama karyawan terlebih dahulu.');
                    setFaceStatus(type, 'Pilih nama karyawan terlebih dahulu.');
                    return;
                }
            }

            descriptorInput.value = '';
            fotoInput.value = '';
            submitButton.disabled = true;

            const modelsReady = await loadFaceModels();

            if (!modelsReady) {
                return;
            }

            try {
                await stopCamera();

                setFaceStatus(type, 'Membuka kamera...', attendanceId);

                currentStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        facingMode: 'user'
                    },
                    audio: false
                });

                video.srcObject = currentStream;
                video.classList.add('active');
                overlayCanvas.classList.add('active');

                await new Promise((resolve) => {
                    video.onloadedmetadata = () => {
                        video.play();
                        resolve();
                    };
                });

                setFaceStatus(type, 'Kamera aktif. Mendeteksi wajah...', attendanceId);

                await new Promise(resolve => setTimeout(resolve, 1000));

                const detection = await faceapi
                    .detectSingleFace(
                        video,
                        new faceapi.TinyFaceDetectorOptions({
                            inputSize: 416,
                            scoreThreshold: 0.45
                        })
                    )
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                const displaySize = {
                    width: video.videoWidth || 640,
                    height: video.videoHeight || 480
                };

                overlayCanvas.width = displaySize.width;
                overlayCanvas.height = displaySize.height;

                const overlayContext = overlayCanvas.getContext('2d');
                overlayContext.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);

                if (!detection) {
                    setFaceStatus(type, 'Wajah tidak terdeteksi. Pastikan wajah terang dan menghadap kamera.', attendanceId);
                    alert('Wajah tidak terdeteksi. Pastikan wajah terlihat jelas, terang, dan menghadap kamera.');

                    await stopCamera();
                    video.classList.remove('active');
                    overlayCanvas.classList.remove('active');
                    return;
                }

                const resizedDetection = faceapi.resizeResults(detection, displaySize);
                drawFaceCircle(overlayCanvas, resizedDetection);

                const descriptor = Array.from(detection.descriptor);

                if (!descriptor || descriptor.length < 100) {
                    setFaceStatus(type, 'Data Face ID tidak valid. Silakan scan ulang.', attendanceId);
                    alert('Data Face ID tidak valid. Silakan scan ulang.');

                    await stopCamera();
                    video.classList.remove('active');
                    overlayCanvas.classList.remove('active');
                    return;
                }

                descriptorInput.value = JSON.stringify(descriptor);

                const snapshotCanvas = document.createElement('canvas');
                snapshotCanvas.width = video.videoWidth || 640;
                snapshotCanvas.height = video.videoHeight || 480;

                const snapshotContext = snapshotCanvas.getContext('2d');
                snapshotContext.drawImage(video, 0, 0, snapshotCanvas.width, snapshotCanvas.height);

                fotoInput.value = snapshotCanvas.toDataURL('image/jpeg', 0.85);

                submitButton.disabled = false;

                setFaceStatus(type, 'Face ID berhasil discan. Lingkaran hijau menunjukkan wajah terdeteksi. Sekarang klik tombol absen.', attendanceId);
                alert('Face ID berhasil discan. Sekarang klik tombol absen.');

                setTimeout(async () => {
                    await stopCamera();

                    video.classList.remove('active');
                    overlayCanvas.classList.remove('active');
                    overlayContext.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
                }, 1500);

            } catch (error) {
                console.error('Scan wajah error:', error);

                setFaceStatus(type, 'Gagal scan wajah. Cek izin kamera atau Console browser.', attendanceId);
                alert('Gagal scan wajah. Pastikan izin kamera sudah diberikan.');

                await stopCamera();

                if (video) {
                    video.classList.remove('active');
                }

                if (overlayCanvas) {
                    overlayCanvas.classList.remove('active');
                }
            }
        }

        function drawFaceCircle(canvas, detection) {
            const ctx = canvas.getContext('2d');
            const box = detection.detection.box;

            const centerX = box.x + box.width / 2;
            const centerY = box.y + box.height / 2;
            const radius = Math.max(box.width, box.height) / 2 + 18;

            ctx.save();

            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
            ctx.lineWidth = 6;
            ctx.strokeStyle = '#22c55e';
            ctx.stroke();

            ctx.fillStyle = 'rgba(34, 197, 94, 0.15)';
            ctx.fill();

            ctx.font = 'bold 20px Arial';
            ctx.fillStyle = '#22c55e';
            ctx.fillText(
                'Face ID Terdeteksi',
                Math.max(10, box.x),
                Math.max(30, box.y - 12)
            );

            ctx.restore();
        }

        window.addEventListener('beforeunload', stopCamera);
    </script>
</x-layouts.app>
