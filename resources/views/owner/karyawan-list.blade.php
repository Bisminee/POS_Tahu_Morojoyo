<x-layouts.app title="Daftar Face ID Karyawan">
    <style>
        body {
            background: #f4f5f7;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .page {
            padding: 32px;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 16px 50px rgba(15, 23, 42, .10);
            border: 1px solid #e5e7eb;
        }

        h1 {
            margin: 0 0 20px;
            font-size: 32px;
            font-weight: 800;
            color: #071331;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .input {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 14px;
        }

        .btn {
            border: none;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-success {
            background: #059669;
            color: white;
        }

        .btn-success:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 16px;
        }

        th {
            text-align: left;
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #111827;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 16px;
        }

        .alert-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .modal-backdrop.active {
            display: flex;
        }

        .modal {
            width: 100%;
            max-width: 560px;
            background: white;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 25px 80px rgba(0,0,0,.25);
        }

        .modal h2 {
            margin: 0 0 12px;
            font-size: 22px;
            color: #071331;
        }

        .modal p {
            color: #475569;
            line-height: 1.5;
        }

        video,
        canvas {
            width: 100%;
            border-radius: 16px;
            background: #111827;
            margin: 10px 0;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .photo-preview {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .status-text {
            font-size: 13px;
            font-weight: 700;
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #f8fafc;
            color: #475569;
        }
    </style>

    <div class="page">
        <div class="card">
            <h1>Daftar Face ID Karyawan</h1>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="top-actions">
                <form method="GET" action="{{ route('owner.karyawan.list') }}">
                    <input class="input" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama karyawan...">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </form>

                <a href="{{ route('attendance.owner') }}" class="btn btn-secondary">
                    Lihat Rekap Absensi
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Karyawan</th>
                        <th>Status Face ID</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($karyawans as $karyawan)
                        <tr>
                            <td>
                                @if ($karyawan->face_photo)
                                    <img class="photo-preview" src="{{ asset('storage/' . $karyawan->face_photo) }}" alt="Face ID">
                                @else
                                    -
                                @endif
                            </td>

                            <td>{{ $karyawan->nama }}</td>

                            <td>
                                @if ($karyawan->face_descriptor)
                                    <span class="badge badge-success">Sudah Terdaftar</span>
                                @else
                                    <span class="badge badge-danger">Belum Ada Face ID</span>
                                @endif
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    onclick="openFaceModal({{ $karyawan->idKaryawan }}, @js($karyawan->nama))">
                                    Daftar / Update Face ID
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Belum ada data karyawan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="faceModal" class="modal-backdrop">
        <div class="modal">
            <h2 id="modalTitle">Daftar Face ID</h2>

            <p>
                Arahkan wajah ke kamera dengan pencahayaan yang cukup.
                Setelah wajah terlihat jelas, klik <strong>Scan Wajah</strong>,
                lalu klik <strong>Simpan Face ID</strong>.
            </p>

            <video id="video" autoplay muted playsinline></video>
            <canvas id="canvas" style="display:none;"></canvas>

            <div id="faceStatus" class="status-text">
                Kamera belum aktif.
            </div>

            <form id="faceForm" method="POST">
                @csrf

                <input type="hidden" name="face_descriptor" id="face_descriptor">
                <input type="hidden" name="foto_base64" id="foto_base64">

                <div class="modal-actions">
                    <button type="button" class="btn btn-primary" onclick="scanFace()">
                        Scan Wajah
                    </button>

                    <button type="submit" class="btn btn-success" id="saveButton" disabled>
                        Simpan Face ID
                    </button>

                    <button type="button" class="btn btn-danger" onclick="closeFaceModal()">
                        Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <script>
        let currentStream = null;
        let faceModelsLoaded = false;

        const MODEL_URL = '/face-models';

        function setStatus(message) {
            const status = document.getElementById('faceStatus');

            if (status) {
                status.innerText = message;
            }

            console.log('[Face ID]', message);
        }

        async function loadFaceModels() {
            if (faceModelsLoaded) {
                return true;
            }

            if (typeof faceapi === 'undefined') {
                setStatus('Library face-api.js belum berhasil dimuat.');
                alert('Library face-api.js belum berhasil dimuat. Pastikan koneksi internet aktif.');
                return false;
            }

            try {
                setStatus('Memuat model Face API...');

                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);

                faceModelsLoaded = true;

                setStatus('Model berhasil dimuat. Kamera siap digunakan.');
                return true;
            } catch (error) {
                console.error('Load model error:', error);
                setStatus('Gagal memuat model Face API. Cek Console browser.');
                alert('Gagal memuat model Face API. Pastikan folder public/face-models sudah benar.');
                return false;
            }
        }

        async function openFaceModal(id, nama) {
            document.getElementById('modalTitle').innerText = 'Daftar Face ID: ' + nama;
            document.getElementById('faceForm').action = '/owner/karyawan/' + id + '/face';
            document.getElementById('face_descriptor').value = '';
            document.getElementById('foto_base64').value = '';
            document.getElementById('saveButton').disabled = true;

            document.getElementById('faceModal').classList.add('active');

            setStatus('Membuka kamera...');

            await startCamera();
        }

        async function startCamera() {
            try {
                const video = document.getElementById('video');

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    setStatus('Browser tidak mendukung akses kamera.');
                    alert('Browser tidak mendukung akses kamera.');
                    return;
                }

                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                    currentStream = null;
                }

                currentStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        facingMode: 'user'
                    },
                    audio: false
                });

                video.srcObject = currentStream;

                await new Promise((resolve) => {
                    video.onloadedmetadata = () => {
                        video.play();
                        resolve();
                    };
                });

                setStatus('Kamera aktif. Arahkan wajah ke kamera lalu klik Scan Wajah.');
            } catch (error) {
                console.error('Camera error:', error);
                setStatus('Gagal membuka kamera. Cek izin kamera browser.');
                alert('Gagal membuka kamera. Izinkan akses kamera di browser.');
            }
        }

        function closeFaceModal() {
            document.getElementById('faceModal').classList.remove('active');

            document.getElementById('face_descriptor').value = '';
            document.getElementById('foto_base64').value = '';
            document.getElementById('saveButton').disabled = true;

            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }

            setStatus('Kamera ditutup.');
        }

        async function scanFace() {
            try {
                const modelsReady = await loadFaceModels();

                if (!modelsReady) {
                    return;
                }

                const video = document.getElementById('video');

                if (!video || !video.srcObject) {
                    setStatus('Kamera belum aktif.');
                    alert('Kamera belum aktif. Tutup modal lalu buka ulang.');
                    return;
                }

                if (video.readyState < 2) {
                    setStatus('Video belum siap.');
                    alert('Video belum siap. Tunggu sebentar lalu klik Scan Wajah lagi.');
                    return;
                }

                setStatus('Mendeteksi wajah...');

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
                    setStatus('Wajah tidak terdeteksi.');
                    alert('Wajah tidak terdeteksi. Pastikan wajah terlihat jelas, terang, dan menghadap kamera.');
                    return;
                }

                const descriptor = Array.from(detection.descriptor);

                if (!descriptor || descriptor.length < 100) {
                    setStatus('Data Face ID tidak valid.');
                    alert('Data Face ID tidak valid. Silakan scan ulang.');
                    return;
                }

                document.getElementById('face_descriptor').value = JSON.stringify(descriptor);

                const canvas = document.getElementById('canvas');
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 480;

                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                document.getElementById('foto_base64').value = canvas.toDataURL('image/jpeg', 0.85);
                document.getElementById('saveButton').disabled = false;

                setStatus('Face ID berhasil discan. Klik Simpan Face ID.');
                alert('Face ID berhasil discan. Klik Simpan Face ID.');
            } catch (error) {
                console.error('Face scan error:', error);
                setStatus('Gagal scan wajah. Detail error ada di Console browser.');
                alert('Gagal scan wajah. Buka F12 > Console untuk melihat detail error.');
            }
        }
    </script>
</x-layouts.app>