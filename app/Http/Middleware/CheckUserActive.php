<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Rotas que devem ser ignoradas pelo middleware
     */
    protected array $except = [
        '/',
        'login',
        'login/*',
        'face/login',
        'face/login/*',
        'cliente/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ignorar rotas públicas
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        if (Auth::check() && !Auth::user()->ativo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Sua conta foi desativada. Entre em contato com o administrador.');
        }

        return $next($request);
    }

    /**
     * Verifica se a rota deve ser ignorada
     */
    protected function shouldSkip(Request $request): bool
    {
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }
        return false;
    }
}
