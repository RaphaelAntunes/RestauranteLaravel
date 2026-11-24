<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;

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
            'loja' => \App\Http\Middleware\ResolveLoja::class,
            'loja.user' => \App\Http\Middleware\EnsureUserBelongsToLoja::class,
            'loja.cliente' => \App\Http\Middleware\EnsureClienteBelongsToLoja::class,
            'platform.admin' => \App\Http\Middleware\EnsurePlatformAdmin::class,
            'active' => \App\Http\Middleware\CheckUserActive::class,
        ]);

        // A loja precisa estar resolvida antes dos model bindings para que o
        // escopo de tenant também proteja URLs contendo IDs de recursos.
        $middleware->prependToPriorityList(
            SubstituteBindings::class,
            \App\Http\Middleware\ResolveLoja::class
        );

        $middleware->appendToGroup('web', \App\Http\Middleware\EncerrarDemoDesativada::class);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
