<?php

namespace App\Http\Middleware;

use App\Support\EntradaDemonstracao;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class EncerrarDemoDesativada
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('demo_cliente_celular') && !EntradaDemonstracao::ativa()) {
            Auth::guard('cliente')->logout();
            $request->session()->forget(['demo_cliente_celular', 'carrinho_session_id']);
            $request->session()->regenerate(true);
        }
        return $next($request);
    }
}
