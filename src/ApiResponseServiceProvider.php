<?php

namespace KodePandai\ApiResponse;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class ApiResponseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-api-response.php',
            'laravel-api-response',
        );
    }

    public function boot(): void
    {
        $this->app->bind('api-response', config('laravel-api-response.response-class'));

        $this->loadJsonTranslationsFrom(__DIR__.'/../lang');

        if (App::runningInConsole()) {
            //
            $this->publishes([
                __DIR__.'/../config/' => base_path('config'),
            ], 'laravel-api-response-config');

            $this->publishes([
                __DIR__.'/../lang/' => base_path('lang/vendor/laravel-api-response'),
            ], 'laravel-api-response-lang');
            //
        }
    }
}
