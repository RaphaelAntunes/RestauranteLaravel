<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFacialObrigatorio
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Verificar se o usuário está autenticado
        if (!$user) {
            return $next($request);
        }

        // Verificar se o reconhecimento facial é obrigatório E o usuário ainda não cadastrou
        if ($user->facial_obrigatorio && is_null($user->face_embedding)) {
            // Não redirecionar se já estiver na página de cadastro facial
            if (!$request->is('face/register') && !$request->is('face/register/*')) {
                return redirect()->route('face.register')
                    ->with('info', 'Você precisa cadastrar seu reconhecimento facial para continuar.');
            }
        }

        return $next($request);
    }
}
