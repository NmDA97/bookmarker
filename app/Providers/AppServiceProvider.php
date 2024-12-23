<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UrlPreviewService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UrlPreviewService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
