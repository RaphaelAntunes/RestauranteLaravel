<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'cliente.auth' => \App\Http\Middleware\ClienteAuth::class,
            'cliente.guest' => \App\Http\Middleware\ClienteGuest::class,
        ]);

        // Verificar se usuário está ativo em todas as requisições web
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckUserActive::class);

        // Verificar se reconhecimento facial é obrigatório
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckFacialObrigatorio::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
