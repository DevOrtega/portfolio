<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Hiking\RouteProviderInterface::class,
            \App\Infrastructure\Services\OsrmService::class
        );
        
        $this->app->bind(
            \App\Domain\Hiking\ElevationProviderInterface::class,
            \App\Infrastructure\Services\ElevationService::class
        );
        
        $this->app->bind(
            \App\Domain\Hiking\PoiProviderInterface::class,
            \App\Infrastructure\Services\DatabasePoiProvider::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production or when behind a proxy (Cloudflare, etc.)
        if ($this->app->environment('production') || 
            request()->header('X-Forwarded-Proto') === 'https' ||
            request()->header('CF-Visitor')) {
            URL::forceScheme('https');
        }
    }
}
