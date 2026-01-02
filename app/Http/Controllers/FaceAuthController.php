<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class FaceAuthController extends Controller
{
    // URL da API Python DeepFace
    private const DEEPFACE_API_URL = 'http://localhost:5000';

    /**
     * Exibir página de cadastro facial (protegida - usuário logado)
     */
    public function showRegisterPage()
    {
        return view('auth.face-register');
    }

    /**
     * Registrar embedding facial do usuário autenticado
     * Recebe array de imagens base64 e extrai embeddings usando DeepFace API
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:3|max:3',
            'images.*' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos. São necessárias 3 imagens em base64.'
            ], 422);
        }

        try {
            $user = Auth::user();
            $images = $request->input('images');
            $embeddings = [];

            // EXTRAIR EMBEDDINGS usando API Python
            foreach ($images as $index => $image) {
                try {
                    $response = Http::timeout(30)->post(self::DEEPFACE_API_URL . '/extract-embedding', [
                        'image' => $image
                    ]);

                    if (!$response->successful()) {
                        $errorDetail = $response->json('detail') ?? 'Erro desconhecido';
                        return response()->json([
                            'success' => false,
                            'message' => "Erro na imagem #" . ($index + 1) . ": " . $errorDetail
                        ], 422);
                    }

                    $data = $response->json();

                    if (!$data['success'] || !isset($data['embedding'])) {
                        return response()->json([
                            'success' => false,
                            'message' => "Falha ao extrair embedding da imagem #" . ($index + 1)
                        ], 422);
                    }

                    $embeddings[] = $data['embedding'];

                    \Log::info("Embedding extracted", [
                        'image_index' => $index + 1,
                        'dimensions' => $data['dimensions'],
                        'magnitude' => $data['magnitude']
                    ]);

                } catch (\Exception $e) {
                    \Log::error('DeepFace API error', ['error' => $e->getMessage()]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao conectar com API de reconhecimento facial. Certifique-se que a API Python está rodando na porta 5000.'
                    ], 500);
                }
            }

            // VALIDAR CONSISTÊNCIA entre os 3 embeddings
            $similarity01 = $this->cosineSimilarity($embeddings[0], $embeddings[1]);
            $similarity02 = $this->cosineSimilarity($embeddings[0], $embeddings[2]);
            $similarity12 = $this->cosineSimilarity($embeddings[1], $embeddings[2]);

            $avgConsistency = ($similarity01 + $similarity02 + $similarity12) / 3;

            if ($avgConsistency < 0.85) {
                return response()->json([
                    'success' => false,
                    'message' => 'As 3 capturas estão muito diferentes entre si (' . round($avgConsistency * 100, 2) . '% de similaridade). Tente manter a mesma posição e iluminação.'
                ], 422);
            }

            // Calcular média dos 3 embeddings (512 dimensões - ArcFace)
            $avgEmbedding = $this->calculateAverageEmbedding($embeddings);

            // Salvar no banco de dados
            $user->face_embedding = $avgEmbedding;
            $user->save();

            \Log::info('Face registration successful (DeepFace)', [
                'user_id' => $user->id,
                'user_name' => $user->nome,
                'consistency' => round($avgConsistency * 100, 2) . '%',
                'embedding_dimensions' => count($avgEmbedding)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reconhecimento facial cadastrado com sucesso! Consistência: ' . round($avgConsistency * 100, 2) . '%'
            ]);

        } catch (\Exception $e) {
            \Log::error('Face registration error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar reconhecimento facial.'
            ], 500);
        }
    }

    /**
     * Exibir página de login facial (pública)
     */
    public function showLoginPage()
    {
        return view('auth.face-login');
    }

    /**
     * Fazer login usando reconhecimento facial com DeepFace API
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Imagem inválida.'
            ], 422);
        }

        try {
            $image = $request->input('image');

            // Buscar todos os usuários com face_embedding cadastrado
            $users = User::whereNotNull('face_embedding')
                        ->where('ativo', true)
                        ->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum usuário com reconhecimento facial cadastrado.'
                ]);
            }

            $bestMatch = null;
            $bestSimilarity = 0;
            $bestDistance = PHP_FLOAT_MAX;

            // Comparar imagem com cada usuário usando DeepFace API
            foreach ($users as $user) {
                try {
                    $response = Http::timeout(30)->post(self::DEEPFACE_API_URL . '/verify', [
                        'image' => $image,
                        'embedding' => $user->face_embedding
                    ]);

                    if (!$response->successful()) {
                        continue; // Pular este usuário se houver erro
                    }

                    $data = $response->json();

                    if ($data['success']) {
                        $match = $data['match'];
                        $similarity = $data['similarity'];
                        $distance = $data['distance'];

                        // Log para auditoria
                        \Log::info('Face recognition attempt (DeepFace)', [
                            'user_id' => $user->id,
                            'user_name' => $user->nome,
                            'similarity' => $similarity . '%',
                            'distance' => $distance,
                            'match' => $match ? 'YES' : 'NO'
                        ]);

                        // Usar o melhor match (menor distância = maior similaridade)
                        if ($match && $distance < $bestDistance) {
                            $bestDistance = $distance;
                            $bestSimilarity = $similarity;
                            $bestMatch = $user;
                        }
                    }

                } catch (\Exception $e) {
                    \Log::error('DeepFace verification error', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            // Se encontrou match válido
            if ($bestMatch) {
                Auth::login($bestMatch);

                // Atualizar último acesso
                $bestMatch->ultimo_acesso = now();
                $bestMatch->save();

                \Log::info('Successful face login (DeepFace)', [
                    'user_id' => $bestMatch->id,
                    'user_name' => $bestMatch->nome,
                    'similarity' => $bestSimilarity . '%',
                    'distance' => $bestDistance
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Login realizado com sucesso!',
                    'similarity' => $bestSimilarity,
                    'redirect' => route('home')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Rosto não reconhecido. Tente novamente ou use login tradicional.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Face login error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar reconhecimento facial. Certifique-se que a API Python está rodando.'
            ], 500);
        }
    }

    /**
     * Remover cadastro facial do usuário autenticado
     */
    public function remove()
    {
        try {
            $user = Auth::user();
            $user->face_embedding = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Reconhecimento facial removido com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover reconhecimento facial.'
            ], 500);
        }
    }

    /**
     * Calcular média de múltiplos embeddings
     */
    private function calculateAverageEmbedding(array $embeddings): array
    {
        $count = count($embeddings);
        $dimensions = count($embeddings[0]);
        $avgEmbedding = array_fill(0, $dimensions, 0);

        foreach ($embeddings as $embedding) {
            for ($i = 0; $i < $dimensions; $i++) {
                $avgEmbedding[$i] += $embedding[$i];
            }
        }

        for ($i = 0; $i < $dimensions; $i++) {
            $avgEmbedding[$i] /= $count;
        }

        return $avgEmbedding;
    }

    /**
     * Calcular Cosine Similarity entre dois vetores
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        if (count($vecA) !== count($vecB)) {
            return 0;
        }

        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;

        for ($i = 0; $i < count($vecA); $i++) {
            $dotProduct += $vecA[$i] * $vecB[$i];
            $magnitudeA += $vecA[$i] * $vecA[$i];
            $magnitudeB += $vecB[$i] * $vecB[$i];
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        if ($magnitudeA == 0 || $magnitudeB == 0) {
            return 0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }

    /**
     * Calcular magnitude de um vetor
     */
    private function calculateMagnitude(array $vec): float
    {
        $sum = 0;
        foreach ($vec as $val) {
            $sum += $val * $val;
        }
        return sqrt($sum);
    }
}
