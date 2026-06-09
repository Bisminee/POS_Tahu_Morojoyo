{{-- resources/views/attendance/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Absensi Masuk')

@section('content')

    @php
        $faceDescriptorRaw = $shift?->karyawan?->face_descriptor ?? null;
    @endphp

    <div class="min-h-screen bg-[#FFF8E7] flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="flex justify-center mb-8">
                 @if(isset($identitas) && $identitas->logo)
                    <img src="{{ asset('storage/' . $identitas->logo) }}" alt="{{ $identitas->nama_brand ?? 'Logo' }}"class="h-20 w-auto">
                @endif
            </div>

            {{-- Main card --}}
            <div class="bg-white rounded-3xl shadow-xl border-t-4 border-[#C0271A] overflow-hidden">

                {{-- Header --}}
                <div class="bg-[#C0271A] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div>
                            <h1 class="text-white font-black text-xl leading-tight" style="font-family:'Bebas Neue',sans-serif">ABSENSI MASUK</h1>
                            <p class="text-red-200 text-xs">{{ now()->translatedFormat('l, d F Y') }} · {{ now()->format('H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[#F5C518] text-xs font-bold uppercase tracking-wide">Face Recognition</p>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-4">

                    {{-- Session messages --}}
                    @if (session('status'))
                        <div class="bg-green-50 border border-green-200 rounded-2xl px-4 py-3 text-green-700 text-sm font-semibold flex items-center gap-2">
                            <span>✅</span> {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-3 text-red-700 text-sm font-semibold flex items-center gap-2">
                            <span>❌</span> {{ session('error') }}
                        </div>
                    @endif

                    {{-- No shift warning --}}
                    @if (!$shift && $todayShifts->isEmpty())
                        <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-4 text-center">
                            <p class="text-red-700 font-bold text-sm">Belum ada jadwal shift hari ini</p>
                            <p class="text-red-500 text-xs mt-1">Hubungi owner untuk membuat jadwal.</p>
                        </div>
                    @endif

                    {{-- Select shift --}}
                    @if (!$shift && $todayShifts->isNotEmpty())
                        <div class="bg-[#FFF8E7] border border-[#F5C518]/40 rounded-2xl px-4 py-3 flex items-start gap-2 text-xs text-gray-600 mb-1">
                            <span class="text-[#F5C518]">👤</span> Pilih karyawan yang sedang bertugas sekarang.
                        </div>
                        <form method="POST" action="{{ route('attendance.select-shift') }}" class="space-y-4">
                            @csrf
                            @if ($errors->any())
                                <div class="bg-red-50 border border-red-200 rounded-xl px-3 py-2 text-red-600 text-xs">{{ $errors->first() }}</div>
                            @endif
                            <div>
                                <label class="block text-xs font-bold text-[#C0271A] mb-1.5 uppercase tracking-wide">Pilih Karyawan</label>
                                <select name="shift_id" class="w-full border-2 border-gray-200 rounded-2xl px-4 py-3 text-sm outline-none focus:border-[#C0271A] focus:ring-4 focus:ring-[#C0271A]/10 bg-gray-50 font-medium">
                                    @foreach ($todayShifts as $s)
                                        <option value="{{ $s->id }}">
                                            {{ $s->karyawan->nama }} — Sesi {{ ucfirst($s->sesi) }}
                                            ({{ substr($s->jam_mulai, 0, 5) }}–{{ substr($s->jam_selesai, 0, 5) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-[#C0271A] hover:bg-[#9B1E13] text-white font-black text-lg py-3 rounded-2xl transition shadow-lg shadow-red-200 flex items-center justify-center gap-2" style="font-family:'Bebas Neue',sans-serif">
                                <span>✓ PILIH &amp; LANJUT ABSENSI</span>
                            </button>
                        </form>
                    @endif

                    {{-- Shift info --}}
                    @if ($shift)
                        @if (!$faceDescriptorRaw)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl px-4 py-3 flex items-start gap-2">
                                <span class="text-yellow-500 text-lg flex-shrink-0">⚠️</span>
                                <div>
                                    <p class="text-yellow-800 text-sm font-bold">Wajah belum didaftarkan</p>
                                    <p class="text-yellow-700 text-xs mt-0.5">Karyawan <strong>{{ $shift->karyawan->nama }}</strong> belum mendaftarkan wajah. Hubungi owner.</p>
                                </div>
                            </div>
                        @endif

                        {{-- Shift detail card --}}
                        <div class="bg-[#FFF8E7] border border-[#F5C518]/30 rounded-2xl px-5 py-4 space-y-2">
                            <p class="text-xs font-black text-[#C0271A] uppercase tracking-widest mb-3">Info Shift</p>
                            <div class="grid grid-cols-2 gap-y-2 text-sm">
                                <span class="text-gray-500">Karyawan</span>
                                <span class="font-bold text-gray-800 text-right">{{ $shift->karyawan->nama }}</span>
                                <span class="text-gray-500">Sesi</span>
                                <span class="font-bold text-gray-800 text-right">{{ ucfirst($shift->sesi) }}</span>
                                <span class="text-gray-500">Jam Masuk</span>
                                <span class="font-bold text-[#C0271A] text-right">{{ substr($shift->jam_mulai, 0, 5) }}</span>
                                <span class="text-gray-500">Jam Selesai</span>
                                <span class="font-bold text-gray-800 text-right">{{ substr($shift->jam_selesai, 0, 5) }}</span>
                                <span class="text-gray-500">Toleransi</span>
                                <span class="font-bold text-gray-800 text-right">{{ $shift->toleransi_menit }} menit</span>
                            </div>
                        </div>
                    @endif

                    {{-- Camera + absen button --}}
                    @if ($shift && $faceDescriptorRaw)

                        {{-- Camera --}}
                        <div class="relative rounded-2xl overflow-hidden bg-gray-900 border-2 border-gray-200" style="aspect-ratio:4/3">
                            <video id="video" autoplay muted playsinline class="w-full h-full object-cover block" style="transform:scaleX(-1)"></video>
                            <canvas id="canvas" class="hidden"></canvas>

                            {{-- Face guide overlay --}}
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div id="face-guide" class="w-44 h-56 rounded-full border-[2.5px] border-white/40 transition-all duration-300" style="box-shadow:0 0 0 9999px rgba(0,0,0,0.4)"></div>
                            </div>

                            {{-- Status badge --}}
                            <div class="absolute bottom-3 left-1/2 -translate-x-1/2">
                                <span id="camera-status" class="bg-black/60 backdrop-blur-sm text-white text-xs font-semibold px-4 py-1.5 rounded-full whitespace-nowrap">Memuat model...</span>
                            </div>

                            {{-- Loading overlay --}}
                            <div id="loading-models" class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center gap-3 text-white text-sm rounded-2xl">
                                <div class="w-10 h-10 border-[3px] border-white/30 border-t-white rounded-full animate-spin"></div>
                                <span>Memuat model pengenalan wajah...</span>
                            </div>
                        </div>

                        {{-- Confidence bar --}}
                        <div id="confidence-wrap" class="hidden space-y-1.5">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Kecocokan wajah</span>
                                <span id="confidence-pct" class="font-bold">0%</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div id="confidence-fill" class="h-full rounded-full transition-all duration-300 bg-gray-200" style="width:0%"></div>
                            </div>
                        </div>

                        {{-- Absen button --}}
                        <button
                            id="btn-absen"
                            disabled
                            onclick="doAbsen()"
                            class="w-full bg-[#C0271A] hover:bg-[#9B1E13] disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-black text-xl py-4 rounded-2xl transition-all shadow-lg shadow-red-200 flex items-center justify-center gap-2 active:scale-[.98]"
                            style="font-family:'Bebas Neue',sans-serif; letter-spacing:.05em"
                        >
                            <span>📸 ABSEN SEKARANG</span>
                        </button>

                        {{-- Status box --}}
                        <div id="status-box" class="hidden rounded-2xl px-4 py-3 text-sm font-semibold text-center"></div>

                    @endif

                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">© 2026 Tahu Bakso Morojoyo · Kelompok 4</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    @if ($shift && $faceDescriptorRaw)
    <script>
        const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const CLOCK_IN_URL = "{{ route('attendance.clock-in') }}";
        const POS_URL      = "{{ route('cashier.pos') }}";
        const MODEL_URL    = '/face-models';
        const MATCH_THRESHOLD = 0.40;

        const RAW_DESCRIPTOR    = @json(is_string($faceDescriptorRaw) ? json_decode($faceDescriptorRaw, true) : $faceDescriptorRaw);
        const KARYAWAN_NAMA     = @json($shift->karyawan->nama);
        const REGISTERED_DESCRIPTOR = new Float32Array(RAW_DESCRIPTOR);

        let faceDetected = false, lastDescriptor = null, currentDistance = 1.0;
        let detectionLoop = null, faceMatchOk = false;

        const video      = document.getElementById('video');
        const canvas     = document.getElementById('canvas');
        const guide      = document.getElementById('face-guide');
        const camStatus  = document.getElementById('camera-status');
        const loadingEl  = document.getElementById('loading-models');
        const btnAbsen   = document.getElementById('btn-absen');
        const statusBox  = document.getElementById('status-box');
        const confWrap   = document.getElementById('confidence-wrap');
        const confFill   = document.getElementById('confidence-fill');
        const confPct    = document.getElementById('confidence-pct');

        const STATUS_CLASSES = {
            ok:   'bg-green-50 border border-green-200 text-green-700',
            err:  'bg-red-50 border border-red-200 text-red-700',
            warn: 'bg-yellow-50 border border-yellow-200 text-yellow-800',
            wait: 'bg-blue-50 border border-blue-200 text-blue-700',
        };

        function showStatus(type, msg) {
            statusBox.className = 'rounded-2xl px-4 py-3 text-sm font-semibold text-center ' + (STATUS_CLASSES[type] || '');
            statusBox.innerHTML = msg;
            statusBox.classList.remove('hidden');
        }

        function updateConfidenceBar(distance) {
            const pct = Math.max(0, Math.min(100, Math.round((1 - distance) * 100)));
            confWrap.classList.remove('hidden');
            confPct.textContent = pct + '%';
            confFill.style.width = pct + '%';
            confFill.style.background = distance <= MATCH_THRESHOLD ? '#10b981' : distance <= 0.55 ? '#f59e0b' : '#ef4444';
        }

        async function initFaceApi() {
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                ]);
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode:'user', width:640, height:480 } });
                video.srcObject = stream;
                await new Promise(r => video.addEventListener('loadedmetadata', r, { once:true }));
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                loadingEl.style.display = 'none';
                camStatus.textContent = 'Arahkan wajah ke kamera';
                startDetectionLoop();
            } catch (err) {
                loadingEl.innerHTML = `<span class="text-red-300">⚠️ ${err.message || 'Gagal akses kamera'}</span>`;
            }
        }

        function startDetectionLoop() {
            detectionLoop = setInterval(async () => {
                try {
                    const det = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold:0.5 }))
                        .withFaceLandmarks(true).withFaceDescriptor();
                    if (det) {
                        faceDetected   = true;
                        lastDescriptor = det.descriptor;
                        currentDistance = faceapi.euclideanDistance(lastDescriptor, REGISTERED_DESCRIPTOR);
                        updateConfidenceBar(currentDistance);
                        if (currentDistance <= MATCH_THRESHOLD) {
                            faceMatchOk = true;
                            guide.style.borderColor = '#34d399';
                            guide.style.boxShadow   = '0 0 0 9999px rgba(0,0,0,0.4), 0 0 24px rgba(52,211,153,.35)';
                            camStatus.textContent   = `✓ Wajah cocok — ${KARYAWAN_NAMA}`;
                            btnAbsen.disabled       = false;
                        } else {
                            faceMatchOk = false;
                            guide.style.borderColor = '#fbbf24';
                            guide.style.boxShadow   = '0 0 0 9999px rgba(0,0,0,0.4)';
                            camStatus.textContent   = `Wajah tidak dikenali (${currentDistance.toFixed(2)})`;
                            btnAbsen.disabled       = true;
                        }
                    } else {
                        faceDetected = false; faceMatchOk = false; lastDescriptor = null; currentDistance = 1.0;
                        guide.style.borderColor = '#f87171';
                        guide.style.boxShadow   = '0 0 0 9999px rgba(0,0,0,0.4)';
                        camStatus.textContent   = 'Wajah tidak terdeteksi...';
                        btnAbsen.disabled       = true;
                        updateConfidenceBar(1.0);
                    }
                } catch(_) {}
            }, 600);
        }

        function capturePhoto() {
            const ctx = canvas.getContext('2d');
            ctx.save(); ctx.scale(-1,1); ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height); ctx.restore();
            return canvas.toDataURL('image/jpeg', 0.8);
        }

        async function doAbsen() {
            if (!faceDetected || !lastDescriptor) { showStatus('warn','Pastikan wajah terdeteksi dulu di kamera.'); return; }
            if (!faceMatchOk || currentDistance > MATCH_THRESHOLD) { showStatus('err',`❌ Wajah tidak cocok dengan ${KARYAWAN_NAMA}.`); return; }
            clearInterval(detectionLoop);
            btnAbsen.disabled = true;
            btnAbsen.innerHTML = '<div class="w-5 h-5 border-2 border-white/40 border-t-white rounded-full animate-spin mr-2"></div> Memverifikasi...';
            showStatus('wait','⏳ Mengirim data absensi...');
            try {
                const res  = await fetch(CLOCK_IN_URL, {
                    method:'POST',
                    headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json' },
                    body: JSON.stringify({ face_descriptor: Array.from(lastDescriptor), face_distance: currentDistance }),
                });
                const data = await res.json();
                if (data.success) {
                    showStatus(data.status_masuk === 'telat' ? 'warn' : 'ok', `${data.status_masuk === 'telat' ? '⚠️' : '✅'} ${data.message}`);
                    setTimeout(() => { window.location.href = data.redirect || POS_URL; }, 900);
                } else {
                    showStatus('err', `❌ ${data.message}`);
                    btnAbsen.disabled = false;
                    btnAbsen.innerHTML = '📸 ABSEN SEKARANG';
                    startDetectionLoop();
                }
            } catch(err) {
                showStatus('err','❌ Terjadi kesalahan koneksi. Coba lagi.');
                btnAbsen.disabled = false;
                btnAbsen.innerHTML = '📸 ABSEN SEKARANG';
                startDetectionLoop();
            }
        }

        initFaceApi();
    </script>
    @endif

@endsection