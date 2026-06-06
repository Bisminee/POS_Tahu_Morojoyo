<x-filament::page>
    <h2>Absen dengan Face Recognition</h2>

    <video id="video" width="400" autoplay></video>
    <button onclick="capture()">Absen</button>

    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js"></script>

    <script>
        const video = document.getElementById('video');

        navigator.mediaDevices.getUserMedia({ video: {} })
            .then(stream => video.srcObject = stream);

        async function loadModels() {
            await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
        }

        loadModels();

        async function capture() {
            const detection = await faceapi.detectSingleFace(video)
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!detection) {
                alert("Wajah tidak terdeteksi!");
                return;
            }

            fetch('/absen-face', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    descriptor: Array.from(detection.descriptor)
                })
            })
            .then(res => res.json())
            .then(data => alert(data.message));
        }
    </script>
</x-filament::page>