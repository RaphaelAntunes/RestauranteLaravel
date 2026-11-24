<?php

namespace App\Http\Middleware;

use App\Support\LojaContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureClienteBelongsToLoja
{
    public function handle(Request $request, Closure $next): Response
    {
        $cliente = Auth::guard('cliente')->user();

        if (!$cliente || (int) $cliente->loja_id !== (int) app(LojaContext::class)->id()) {
            Auth::guard('cliente')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('cliente.login', ['loja' => app(LojaContext::class)->loja()])
                ->with('error', 'Entre com uma conta desta loja.');
        }

        return $next($request);
    }
}
