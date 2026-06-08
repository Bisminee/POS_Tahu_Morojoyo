@props(['title' => 'Absensi Kasir'])

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
            max-width: 760px;
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

        .camera-box video {
            width: 100%;
            border-radius: 16px;
            background: #111827;
            display: none;
            margin-bottom: 12px;
        }

        .camera-box video.active {
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
    </style>

    <div class="attendance-page">
        <div class="attendance-card">
            <div class="attendance-header">
                <h1>Absensi Kasir</h1>
                <p>{{ now()->translatedFormat('l, d F Y • H:i') }}</p>
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

            @if ($activeAttendance)
                <div class="active-box">
                    <h2>Shift Sedang Aktif</h2>
                    <p><strong>Karyawan:</strong> {{ $activeAttendance->karyawan?->nama ?? '-' }}</p>
                    <p><strong>Jam Masuk:</strong> {{ $activeAttendance->jam_masuk?->format('H:i') ?? '-' }}</p>
                </div>

                <a href="{{ route('cashier.pos') }}" class="btn-secondary">
                    Masuk ke POS
                </a>

                <form method="POST" action="{{ route('attendance.clock-out') }}" id="clockOutForm" style="margin-top: 12px;">
                    @csrf

                    <input type="hidden" name="face_descriptor" id="face_descriptor_out">
                    <input type="hidden" name="foto_base64" id="foto_base64_out">

                    <div class="camera-box">
                        <div class="face-status" id="faceStatusOut">
                            Untuk absen pulang, scan wajah terlebih dahulu.
                        </div>

                        <video id="videoOut" autoplay muted playsinline></video>

                        <div class="button-gap">
                            <button type="button" class="btn-secondary"
                                onclick="scanFace('out')">
                                Scan Wajah Pulang
                            </button>

                            <button type="submit" class="btn btn-danger" id="clockOutSubmit" disabled>
                                Selesai Shift / Absen Pulang
                            </button>
                        </div>
                    </div>
                </form>
            @else
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

                            <video id="videoIn" autoplay muted playsinline></video>

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
                        Belum ada data karyawan aktif. Hubungi owner.
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <script>
        let faceModelsLoaded = false;
        let currentStream = null;

        const MODEL_URL = '/face-models';

        function setFaceStatus(type, message) {
            const statusId = type === 'in' ? 'faceStatusIn' : 'faceStatusOut';
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

        async function scanFace(type) {
            const isClockIn = type === 'in';

            const videoId = isClockIn ? 'videoIn' : 'videoOut';
            const descriptorInputId = isClockIn ? 'face_descriptor_in' : 'face_descriptor_out';
            const fotoInputId = isClockIn ? 'foto_base64_in' : 'foto_base64_out';
            const submitButtonId = isClockIn ? 'clockInSubmit' : 'clockOutSubmit';

            const video = document.getElementById(videoId);
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

                setFaceStatus(type, 'Membuka kamera...');

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

                await new Promise((resolve) => {
                    video.onloadedmetadata = () => {
                        video.play();
                        resolve();
                    };
                });

                setFaceStatus(type, 'Kamera aktif. Mengambil data wajah...');

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

                if (!detection) {
                    setFaceStatus(type, 'Wajah tidak terdeteksi. Pastikan wajah terang dan menghadap kamera.');
                    alert('Wajah tidak terdeteksi. Pastikan wajah terlihat jelas, terang, dan menghadap kamera.');
                    await stopCamera();
                    video.classList.remove('active');
                    return;
                }

                const descriptor = Array.from(detection.descriptor);

                if (!descriptor || descriptor.length < 100) {
                    setFaceStatus(type, 'Data Face ID tidak valid. Silakan scan ulang.');
                    alert('Data Face ID tidak valid. Silakan scan ulang.');
                    await stopCamera();
                    video.classList.remove('active');
                    return;
                }

                descriptorInput.value = JSON.stringify(descriptor);

                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 480;

                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                fotoInput.value = canvas.toDataURL('image/jpeg', 0.85);

                submitButton.disabled = false;

                setFaceStatus(type, 'Face ID berhasil discan. Sekarang klik tombol absen.');
                alert('Face ID berhasil discan. Sekarang klik tombol absen.');

                await stopCamera();
                video.classList.remove('active');
            } catch (error) {
                console.error('Scan wajah error:', error);
                setFaceStatus(type, 'Gagal scan wajah. Cek izin kamera atau Console browser.');
                alert('Gagal scan wajah. Pastikan izin kamera sudah diberikan.');
                await stopCamera();

                if (video) {
                    video.classList.remove('active');
                }
            }
        }

        window.addEventListener('beforeunload', stopCamera);
    </script>
</x-layouts.app>