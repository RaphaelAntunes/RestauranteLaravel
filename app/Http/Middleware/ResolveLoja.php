<?php

namespace App\Http\Middleware;

use App\Models\Loja;
use App\Support\LojaContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

final class ResolveLoja
{
    public function handle(Request $request, Closure $next): Response
    {
        $loja = $request->route('loja');

        if (!$loja instanceof Loja) {
            $loja = Loja::where('slug', (string) $loja)->firstOrFail();
            $request->route()->setParameter('loja', $loja);
        }

        abort_unless($loja->ativa(), 404);

        $context = app(LojaContext::class);
        $previousLoja = $context->loja();
        $context->set($loja);
        URL::defaults(['loja' => $loja->slug]);
        View::share('lojaAtual', $loja);
        try {
            return $next($request);
        } finally {
            if ($previousLoja) {
                $context->set($previousLoja);
                URL::defaults(['loja' => $previousLoja->slug]);
            } else {
                $context->clear();
            }
        }
    }
}
