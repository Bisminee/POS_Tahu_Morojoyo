<x-layouts.app title="Daftar Face ID Karyawan">
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
                    },
                    fontFamily: {
                        jakarta: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>

    <div class="min-h-screen bg-brand-cream px-4 py-8 md:px-8">

        {{-- Brand Header --}}
        <div class="text-center mb-6">
            <p class="text-xs font-bold tracking-[0.2em] text-brand-red/40 uppercase">· Tahu Bakso Morojoyo ·</p>
        </div>
        {{-- <div class="flex justify-center mb-8">
                @if(isset($identitas) && $identitas->logo)
                    <img src="{{ asset('storage/' . $identitas->logo) }}" alt="{{ $identitas->nama_brand ?? 'Logo' }}"class="h-20 w-auto">
                @endif
            </div> --}}

        <div class="max-w-6xl mx-auto">
            {{-- Page Card --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-brand-red/10 overflow-hidden border border-brand-cream-dk">

                {{-- Red Header --}}
                <div class="bg-brand-red to-brand-red-dk px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-extrabold tracking-widest uppercase text-white">Daftar Face ID Karyawan</h1>
                        <p class="text-sm text-red-200 mt-0.5">Kelola data biometrik seluruh karyawan</p>
                    </div>
                    <a href="{{ route('attendance.owner') }}"
                       class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 text-white text-sm font-bold px-4 py-2 rounded-xl transition-colors border border-white/20">
                        Lihat Rekap Absensi
                    </a>
                </div>

                <div class="p-6">

                    @if (session('success'))
                        <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm font-semibold px-4 py-3 rounded-xl">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold px-4 py-3 rounded-xl">
                            @foreach ($errors->all() as $error)
                                <div>⚠️ {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Search Bar --}}
                    <form method="GET" action="{{ route('owner.karyawan.list') }}" class="flex gap-2 mb-6">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Cari nama karyawan..."
                            class="flex-1 border border-brand-cream-dk rounded-xl px-4 py-2.5 text-sm bg-brand-cream focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition">
                        <button type="submit"
                                class="bg-brand-red hover:bg-brand-red-dk text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
                            Cari
                        </button>
                    </form>

                    {{-- Table --}}
                    <div class="overflow-x-auto rounded-xl border border-brand-cream-dk">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-brand-cream">
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Foto</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Nama Karyawan</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Status Face ID</th>
                                    <th class="text-left text-xs font-bold uppercase tracking-wider text-brand-red/60 px-4 py-3 border-b border-brand-cream-dk">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-brand-cream-dk">
                                @forelse ($karyawans as $karyawan)
                                    <tr class="hover:bg-brand-cream/50 transition-colors">
                                        <td class="px-4 py-3">
                                            @if ($karyawan->face_photo)
                                                <img src="{{ asset('storage/' . $karyawan->face_photo) }}"
                                                     alt="Face ID"
                                                     class="w-14 h-14 object-cover rounded-xl border-2 border-brand-cream-dk">
                                            @else
                                                <div class="w-14 h-14 bg-brand-cream rounded-xl border-2 border-dashed border-brand-cream-dk flex items-center justify-center text-xl">X</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $karyawan->nama }}</td>
                                        <td class="px-4 py-3">
                                            @if ($karyawan->face_descriptor)
                                                <span class="inline-flex items-center gap-1 bg-green-50 text-green-800 border border-green-200 text-xs font-bold px-3 py-1.5 rounded-full">
                                                    ✓ Sudah Terdaftar
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 bg-red-50 text-brand-red border border-red-200 text-xs font-bold px-3 py-1.5 rounded-full">
                                                    ✗ Belum Ada Face ID
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <button
                                                type="button"
                                                onclick="openFaceModal({{ $karyawan->idKaryawan }}, @js($karyawan->nama))"
                                                class="bg-brand-red hover:bg-brand-red-dk text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors">
                                                Daftar / Update
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-gray-400 py-10 text-sm">
                                            Belum ada data karyawan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Face ID --}}
    <div id="faceModal"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center px-4">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden border border-brand-cream-dk">

            {{-- Modal Header --}}
            <div class="bg-brand-red-dk to-brand-red-dk px-6 py-4">
                <h2 id="modalTitle" class="text-lg font-extrabold tracking-widest uppercase text-white">Daftar Face ID</h2>
                <p class="text-red-200 text-xs mt-0.5">Arahkan wajah ke kamera dengan pencahayaan cukup.</p>
            </div>

            <div class="p-5 space-y-4">
                {{-- Camera --}}
                <div class="relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3]">
                    <video id="video" autoplay muted playsinline class="w-full h-full object-cover"></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    <div id="faceStatus"
                         class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 text-white text-[11px] font-medium px-3 py-1 rounded-full backdrop-blur-sm whitespace-nowrap">
                        Kamera belum aktif.
                    </div>
                </div>

                <form id="faceForm" method="POST">
                    @csrf
                    <input type="hidden" name="face_descriptor" id="face_descriptor">
                    <input type="hidden" name="foto_base64" id="foto_base64">

                    <div class="flex gap-2 flex-wrap">
                        <button type="button" onclick="scanFace()"
                                class="flex-1 bg-brand-red hover:bg-brand-red-dk text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                            Scan Wajah
                        </button>
                        <button type="submit" id="saveButton" disabled
                                class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                            Simpan Face ID
                        </button>
                        <button type="button" onclick="closeFaceModal()"
                                class="bg-brand-cream-dk hover:bg-brand-cream-dk/70 text-gray-700 text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                            Tutup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        let currentStream = null;
        let faceModelsLoaded = false;
        const MODEL_URL = '/face-models';

        function setStatus(message) {
            const status = document.getElementById('faceStatus');
            if (status) status.innerText = message;
        }

        async function loadFaceModels() {
            if (faceModelsLoaded) return true;
            if (typeof faceapi === 'undefined') {
                setStatus('Library face-api.js belum berhasil dimuat.');
                return false;
            }
            try {
                setStatus('Memuat model Face API...');
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                faceModelsLoaded = true;
                setStatus('Model berhasil dimuat. Kamera siap.');
                return true;
            } catch (error) {
                setStatus('Gagal memuat model Face API.');
                return false;
            }
        }

        async function openFaceModal(id, nama) {
            document.getElementById('modalTitle').innerText = 'Daftar Face ID: ' + nama;
            document.getElementById('faceForm').action = '/owner/karyawan/' + id + '/face';
            document.getElementById('face_descriptor').value = '';
            document.getElementById('foto_base64').value = '';
            document.getElementById('saveButton').disabled = true;
            document.getElementById('faceModal').classList.remove('hidden');
            document.getElementById('faceModal').classList.add('flex');
            setStatus('Membuka kamera...');
            await startCamera();
        }

        async function startCamera() {
            try {
                const video = document.getElementById('video');
                if (!navigator.mediaDevices?.getUserMedia) {
                    setStatus('Browser tidak mendukung akses kamera.');
                    return;
                }
                if (currentStream) {
                    currentStream.getTracks().forEach(t => t.stop());
                    currentStream = null;
                }
                currentStream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                    audio: false
                });
                video.srcObject = currentStream;
                await new Promise(resolve => { video.onloadedmetadata = () => { video.play(); resolve(); }; });
                setStatus('Kamera aktif. Arahkan wajah ke kamera lalu klik Scan Wajah.');
            } catch (error) {
                setStatus('Gagal membuka kamera. Izinkan akses kamera di browser.');
            }
        }

        function closeFaceModal() {
            document.getElementById('faceModal').classList.add('hidden');
            document.getElementById('faceModal').classList.remove('flex');
            document.getElementById('face_descriptor').value = '';
            document.getElementById('foto_base64').value = '';
            document.getElementById('saveButton').disabled = true;
            if (currentStream) { currentStream.getTracks().forEach(t => t.stop()); currentStream = null; }
            setStatus('Kamera ditutup.');
        }

        async function scanFace() {
            try {
                const modelsReady = await loadFaceModels();
                if (!modelsReady) return;
                const video = document.getElementById('video');
                if (!video?.srcObject || video.readyState < 2) {
                    setStatus('Kamera belum siap. Tunggu sebentar lalu coba lagi.');
                    return;
                }
                setStatus('Mendeteksi wajah...');
                const detection = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 416, scoreThreshold: 0.45 }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();
                if (!detection) {
                    setStatus('Wajah tidak terdeteksi. Pastikan wajah terlihat jelas.');
                    return;
                }
                const descriptor = Array.from(detection.descriptor);
                if (!descriptor || descriptor.length < 100) {
                    setStatus('Data Face ID tidak valid. Silakan scan ulang.');
                    return;
                }
                document.getElementById('face_descriptor').value = JSON.stringify(descriptor);
                const canvas = document.getElementById('canvas');
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 480;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                document.getElementById('foto_base64').value = canvas.toDataURL('image/jpeg', 0.85);
                document.getElementById('saveButton').disabled = false;
                setStatus('✓ Face ID berhasil discan. Klik Simpan Face ID.');
            } catch (error) {
                setStatus('Gagal scan wajah. Buka Console browser untuk detail.');
            }
        }
    </script>
</x-layouts.app>