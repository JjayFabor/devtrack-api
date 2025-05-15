<?php

namespace App\Providers;

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
        config(['l5-swagger.defaults.constants.L5_SWAGGER_CONST_DOCS_URL' => 'docs/api-docs.json']);

        View::composer('l5-swagger::index', function ($view) {
            $view->with('urlsToDocs', [
                config('l5-swagger.default.documentations.default.api.title', 'L5 Swagger UI')
                    => url('docs/api-docs.json')
            ]);
        });
    }
}
