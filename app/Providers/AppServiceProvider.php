<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        // Share the API key usage data with all views
        // View::composer('*', function ($view) {
        //     $apiKeyUsage = auth()->user() ? auth()->user()->apiKeys()->with('usage')->get() : null;
        //     $view->with('apiKeyUsage', $apiKeyUsage);
        // });
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
