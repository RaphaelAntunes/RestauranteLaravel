<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request): void
    {
        Schema::defaultStringLength(191);

        // Força HTTPS apenas se APP_URL usar https ou se vier de proxy com https
        if (str_starts_with(config('app.url'), 'https') || $request->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}
