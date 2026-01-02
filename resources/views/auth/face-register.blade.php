<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cadastro Facial - Sistema Restaurante</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-r from-indigo-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full mx-4">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Cadastro de Reconhecimento Facial</h1>
                <p class="text-gray-600 mt-2">Olá, {{ auth()->user()->nome }}!</p>
                <p class="text-sm text-gray-500 mt-1">Vamos capturar 3 imagens do seu rosto para garantir precisão</p>
            </div>

            <!-- Status Messages -->
            <div id="status-message" class="mb-4 hidden"></div>

            <!-- Progress Indicator -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Progresso</span>
                    <span class="text-sm font-medium text-gray-700"><span id="capture-count">0</span>/3 capturas</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="progress-bar" class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

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
                    id="capture-btn"
                    disabled
                    class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold py-3 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105"
                >
                    Capturar Imagem 1
                </button>

                @if(auth()->user()->face_embedding)
                <button
                    id="remove-btn"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200"
                >
                    Remover Cadastro Facial Atual
                </button>
                @endif

                <a
                    href="{{ route('home') }}"
                    class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-4 rounded-lg transition duration-200"
                >
                    Voltar ao Dashboard
                </a>
            </div>

            <!-- Info Box -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="font-semibold text-blue-900 mb-2">Informações de Privacidade</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>✓ Nenhuma imagem do seu rosto é armazenada</li>
                    <li>✓ Apenas dados matemáticos (embeddings) são salvos</li>
                    <li>✓ Você pode remover seus dados a qualquer momento</li>
                    <li>✓ Conforme LGPD - Lei Geral de Proteção de Dados</li>
                </ul>
            </div>
        </div>

        <p class="text-center text-white text-sm mt-4">
            &copy; {{ date('Y') }} Sistema Restaurante. Seus dados estão protegidos.
        </p>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('overlay');
        const loadingDiv = document.getElementById('loading');
        const captureBtn = document.getElementById('capture-btn');
        const removeBtn = document.getElementById('remove-btn');
        const statusMessage = document.getElementById('status-message');
        const loadingText = document.getElementById('loading-text');
        const captureCount = document.getElementById('capture-count');
        const progressBar = document.getElementById('progress-bar');

        let images = []; // Array de imagens base64
        let captureIndex = 0;

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

        // Atualizar progresso
        function updateProgress() {
            captureCount.textContent = captureIndex;
            const percentage = (captureIndex / 3) * 100;
            progressBar.style.width = percentage + '%';
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
                    captureBtn.disabled = false;
                    showMessage('Câmera iniciada. Posicione seu rosto e clique em "Capturar".', 'info');
                });
            } catch (error) {
                console.error('Erro ao acessar câmera:', error);
                showMessage('Erro ao acessar câmera. Verifique as permissões.', 'error');
                loadingDiv.classList.add('hidden');
            }
        }

        // Capturar imagem da webcam como base64
        async function captureFace() {
            if (captureIndex >= 3) return;

            captureBtn.disabled = true;
            captureBtn.textContent = 'Capturando...';
            showMessage('Capturando imagem...', 'info');

            try {
                // Capturar frame do vídeo
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Converter para base64
                const imageBase64 = canvas.toDataURL('image/jpeg', 0.9);

                // Salvar imagem
                images.push(imageBase64);
                captureIndex++;
                updateProgress();

                // Feedback visual - flash branco
                ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                setTimeout(() => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }, 200);

                if (captureIndex < 3) {
                    showMessage(`Captura ${captureIndex}/3 concluída! Aguarde 1 segundo para a próxima...`, 'success');

                    // Aguardar 1 segundo antes de permitir próxima captura
                    setTimeout(() => {
                        captureBtn.disabled = false;
                        captureBtn.textContent = `Capturar Imagem ${captureIndex + 1}`;
                        showMessage(`Prepare-se para a captura ${captureIndex + 1}/3`, 'info');
                    }, 1000);
                } else {
                    showMessage('Todas as capturas concluídas! Processando com DeepFace...', 'success');
                    await saveImages();
                }

            } catch (error) {
                console.error('Erro na captura:', error);
                showMessage('Erro ao capturar imagem. Tente novamente.', 'error');
                captureBtn.disabled = false;
                captureBtn.textContent = `Capturar Imagem ${captureIndex + 1}`;
            }
        }

        // Enviar imagens para o servidor (que vai chamar a API DeepFace)
        async function saveImages() {
            try {
                captureBtn.textContent = 'Processando com DeepFace...';

                const response = await fetch('{{ route("face.register.post") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ images })
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('✅ Reconhecimento facial cadastrado com sucesso usando DeepFace (ArcFace)!', 'success');
                    captureBtn.textContent = 'Cadastro Concluído!';
                    captureBtn.disabled = true;

                    setTimeout(() => {
                        window.location.href = '{{ route("home") }}';
                    }, 2000);
                } else {
                    showMessage(result.message || 'Erro ao cadastrar reconhecimento facial.', 'error');
                    resetCapture();
                }

            } catch (error) {
                console.error('Erro ao salvar:', error);
                showMessage('Erro ao salvar reconhecimento facial. Certifique-se que a API Python está rodando na porta 5000.', 'error');
                resetCapture();
            }
        }

        // Remover cadastro facial
        if (removeBtn) {
            removeBtn.addEventListener('click', async () => {
                if (!confirm('Tem certeza que deseja remover seu cadastro facial?')) return;

                try {
                    const response = await fetch('{{ route("face.remove") }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        showMessage('Cadastro facial removido com sucesso!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showMessage(result.message || 'Erro ao remover cadastro.', 'error');
                    }
                } catch (error) {
                    console.error('Erro ao remover:', error);
                    showMessage('Erro ao remover cadastro facial.', 'error');
                }
            });
        }

        function resetCapture() {
            images = [];
            captureIndex = 0;
            updateProgress();
            captureBtn.disabled = false;
            captureBtn.textContent = 'Capturar Imagem 1';
        }

        // Event Listeners
        captureBtn.addEventListener('click', captureFace);

        // Inicialização
        (async () => {
            await startCamera();
        })();
    </script>
</body>
</html>
