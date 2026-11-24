<?php

namespace App\Providers;

use App\Support\LojaContext;
use App\Routing\TenantControllerDispatcher;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LojaContext::class);
        $this->app->singleton(
            ControllerDispatcherContract::class,
            fn ($app) => new TenantControllerDispatcher($app)
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request): void
    {
        Schema::defaultStringLength(191);
        URL::forceScheme('https');
        if ($request->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}
