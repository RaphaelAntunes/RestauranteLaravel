<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClienteAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('cliente')->check()) {
            return redirect()->guest(route('cliente.login', [
                'loja' => $request->segment(2),
            ]));
        }

        if (!Auth::guard('cliente')->user()->ativo) {
            Auth::guard('cliente')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('cliente.login')->with('error', 'Este cadastro está temporariamente indisponível.');
        }

        return $next($request);
    }
}
