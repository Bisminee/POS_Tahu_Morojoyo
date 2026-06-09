{{-- resources/views/owner/karyawan-face.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Face ID — {{ $karyawan->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red:        '#C0392B',
                            'red-dark': '#96281B',
                            'red-light':'#E74C3C',
                            cream:      '#FAF6EF',
                            'cream-dark':'#F0E9DC',
                            warm:       '#7B3F2B',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin .8s linear infinite; }
    </style>
</head>

<body class="min-h-screen bg-brand-cream flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-md">

        {{-- Brand mini-header --}}
        <div class="text-center mb-6">
            <p class="text-xs font-bold tracking-[0.2em] text-brand-red/50 uppercase">· Tahu Bakso Morojoyo ·</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-brand-red/10 overflow-hidden border border-brand-cream-dark">

            {{-- Red header --}}
            <div class="bg-gradient-to-br from-brand-red to-brand-red-dark px-6 py-5">
                <h2 class="text-lg font-extrabold tracking-widest uppercase text-white">Daftarkan Face ID</h2>
                <p class="text-sm text-red-200 mt-0.5">
                    Karyawan: <strong class="text-white">{{ $karyawan->nama }}</strong>
                </p>
            </div>

            <div class="p-5 space-y-4">

                {{-- Camera --}}
                <div class="relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3]">
                    <video id="video" autoplay muted playsinline
                        class="w-full h-full object-cover" style="transform: scaleX(-1)"></video>
                    <canvas id="canvas" class="hidden"></canvas>

                    {{-- Face oval guide --}}
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div id="face-guide"
                            class="w-44 h-56 rounded-full border-[2.5px] border-white/50 transition-colors duration-300"
                            style="box-shadow: 0 0 0 9999px rgba(0,0,0,0.38)">
                        </div>
                    </div>

                    {{-- Status pill --}}
                    <div id="cam-status"
                        class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 text-white text-[11px] font-medium px-3 py-1 rounded-full backdrop-blur-sm whitespace-nowrap">
                        Memuat model...
                    </div>

                    {{-- Loading overlay --}}
                    <div id="loading-overlay"
                        class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center gap-3 text-white text-sm">
                        <div class="spinner w-8 h-8 rounded-full border-[3px] border-white/30 border-t-white"></div>
                        <span>Memuat model wajah...</span>
                    </div>
                </div>

                {{-- Save button --}}
                <button
                    id="btn-save"
                    disabled
                    onclick="saveFace()"
                    class="w-full py-3.5 rounded-xl bg-gradient-to-r from-brand-red to-brand-red-light text-white font-extrabold text-sm tracking-[0.12em] uppercase shadow-md shadow-brand-red/25 hover:from-brand-red-dark hover:to-brand-red active:scale-[0.98] transition-all disabled:from-gray-300 disabled:to-gray-300 disabled:text-gray-400 disabled:cursor-not-allowed disabled:shadow-none flex items-center justify-center gap-2"
                >
                    📸 Ambil & Simpan Face ID
                </button>

                {{-- Status box --}}
                <div id="status-box" class="hidden rounded-xl px-4 py-3 text-sm font-semibold text-center"></div>

                {{-- Back link --}}
                <a href="{{ url()->previous() }}"
                    class="flex items-center justify-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-brand-red transition-colors pt-1">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const SAVE_URL  = "{{ route('owner.karyawan.save-face', $karyawan->idKaryawan) }}";
        const CSRF      = "{{ csrf_token() }}";
        const MODEL_URL = '/face-models';

        let lastDescriptor = null;
        const video     = document.getElementById('video');
        const canvas    = document.getElementById('canvas');
        const guide     = document.getElementById('face-guide');
        const camStatus = document.getElementById('cam-status');
        const loading   = document.getElementById('loading-overlay');
        const btn       = document.getElementById('btn-save');
        const statusBox = document.getElementById('status-box');

        async function init() {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            ]);
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: 640, height: 480 }
            });
            video.srcObject = stream;
            await new Promise(r => video.addEventListener('loadedmetadata', r, { once: true }));
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            loading.style.display = 'none';
            camStatus.textContent  = 'Arahkan wajah ke kamera';
            startLoop();
        }

        function startLoop() {
            setInterval(async () => {
                const det = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.5 }))
                    .withFaceLandmarks(true)
                    .withFaceDescriptor();

                if (det) {
                    lastDescriptor = Array.from(det.descriptor);
                    // Green border on detection
                    guide.style.borderColor = '#22c55e';
                    camStatus.textContent   = '✓ Wajah terdeteksi — siap disimpan';
                    btn.disabled            = false;
                } else {
                    lastDescriptor          = null;
                    guide.style.borderColor = 'rgba(255,255,255,0.5)';
                    camStatus.textContent   = 'Arahkan wajah ke kamera...';
                    btn.disabled            = true;
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
            btn.disabled     = true;
            btn.textContent  = 'Menyimpan...';

            try {
                const res  = await fetch(SAVE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ face_descriptor: lastDescriptor, foto_base64: capturePhoto() }),
                });
                const data = await res.json();

                statusBox.classList.remove('hidden');
                if (data.success) {
                    statusBox.className = 'rounded-xl px-4 py-3 text-sm font-semibold text-center bg-green-50 text-green-800 border border-green-200';
                    btn.textContent = '✅ Tersimpan';
                } else {
                    statusBox.className = 'rounded-xl px-4 py-3 text-sm font-semibold text-center bg-red-50 text-red-700 border border-red-200';
                    btn.disabled    = false;
                    btn.textContent = '📸 Ambil & Simpan Face ID';
                }
                statusBox.textContent = data.message;
            } catch (e) {
                statusBox.classList.remove('hidden');
                statusBox.className = 'rounded-xl px-4 py-3 text-sm font-semibold text-center bg-red-50 text-red-700 border border-red-200';
                statusBox.textContent = 'Gagal koneksi. Coba lagi.';
                btn.disabled    = false;
                btn.textContent = '📸 Ambil & Simpan Face ID';
            }
        }

        init();
    </script>
</body>
</html>