<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Facial - Sistema Restaurante</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-r from-indigo-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full mx-4">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Login Facial</h1>
                <p class="text-gray-600 mt-2">Posicione seu rosto na câmera</p>
            </div>

            <!-- Status Messages -->
            <div id="status-message" class="mb-4 hidden"></div>

            <!-- Video/Canvas Container -->
            <div class="relative mb-6">
                <div class="relative overflow-hidden rounded-lg bg-gray-900" style="height: 480px;">
                    <video id="video" autoplay muted playsinline class="absolute inset-0 w-full h-full object-cover"></video>
                    <canvas id="overlay" class="absolute inset-0 w-full h-full"></canvas>
                </div>

                <!-- Loading Overlay -->
                <div id="loading" class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center rounded-lg">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-white mb-4"></div>
                        <p class="text-white font-medium" id="loading-text">Carregando modelos...</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <button
                    id="scan-btn"
                    disabled
                    class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold py-3 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105"
                >
                    Iniciar Reconhecimento
                </button>

                <a
                    href="{{ route('login') }}"
                    class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-4 rounded-lg transition duration-200"
                >
                    Voltar ao Login Tradicional
                </a>
            </div>
        </div>

        <p class="text-center text-white text-sm mt-4">
            &copy; {{ date('Y') }} Sistema Restaurante. Seus dados biométricos não são armazenados como imagem.
        </p>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('overlay');
        const loadingDiv = document.getElementById('loading');
        const scanBtn = document.getElementById('scan-btn');
        const statusMessage = document.getElementById('status-message');
        const loadingText = document.getElementById('loading-text');

        let isScanning = false;

        // Função para exibir mensagens
        function showMessage(message, type = 'info') {
            statusMessage.className = `mb-4 p-4 rounded-lg ${
                type === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' :
                type === 'error' ? 'bg-red-100 border-l-4 border-red-500 text-red-700' :
                'bg-blue-100 border-l-4 border-blue-500 text-blue-700'
            }`;
            statusMessage.textContent = message;
            statusMessage.classList.remove('hidden');
        }

        // Iniciar câmera
        async function startCamera() {
            try {
                loadingText.textContent = 'Iniciando câmera...';

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        facingMode: 'user'
                    }
                });
                video.srcObject = stream;

                video.addEventListener('loadedmetadata', () => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    loadingDiv.classList.add('hidden');
                    scanBtn.disabled = false;
                    showMessage('Câmera iniciada. Posicione seu rosto e clique em "Iniciar Reconhecimento".', 'info');
                });
            } catch (error) {
                console.error('Erro ao acessar câmera:', error);
                showMessage('Erro ao acessar câmera. Verifique as permissões.', 'error');
                loadingDiv.classList.add('hidden');
            }
        }

        // Capturar imagem e fazer login com DeepFace
        async function detectAndLogin() {
            if (isScanning) return;

            isScanning = true;
            scanBtn.disabled = true;
            scanBtn.textContent = 'Capturando...';
            showMessage('Capturando imagem...', 'info');

            try {
                // Capturar frame do vídeo
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Converter para base64
                const imageBase64 = canvas.toDataURL('image/jpeg', 0.9);

                // Feedback visual - flash branco
                ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                setTimeout(() => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }, 200);

                // Enviar imagem para o servidor (DeepFace API)
                scanBtn.textContent = 'Processando com DeepFace...';
                showMessage('Processando com DeepFace (ArcFace)...', 'info');

                const response = await fetch('{{ route("face.login.post") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        image: imageBase64
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showMessage(`✅ Login realizado com sucesso! Similaridade: ${result.similarity}%`, 'success');
                    scanBtn.textContent = 'Redirecionando...';
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1500);
                } else {
                    showMessage(result.message || 'Rosto não reconhecido.', 'error');
                    resetScanButton();
                }

            } catch (error) {
                console.error('Erro no reconhecimento facial:', error);
                showMessage('Erro ao processar reconhecimento facial. Certifique-se que a API Python está rodando na porta 5000.', 'error');
                resetScanButton();
            }
        }

        function resetScanButton() {
            isScanning = false;
            scanBtn.disabled = false;
            scanBtn.textContent = 'Tentar Novamente';
        }

        // Event Listeners
        scanBtn.addEventListener('click', detectAndLogin);

        // Inicialização
        (async () => {
            await startCamera();
        })();
    </script>
</body>
</html>
