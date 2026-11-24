<?php

namespace App\Http\Middleware;

use App\Support\LojaContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserBelongsToLoja
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || (int) $user->loja_id !== (int) app(LojaContext::class)->id()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login', ['loja' => app(LojaContext::class)->loja()])
                ->with('error', 'Entre com uma conta desta loja.');
        }

        return $next($request);
    }
}
