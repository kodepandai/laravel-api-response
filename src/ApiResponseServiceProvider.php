<?php

namespace KodePandai\ApiResponse;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class ApiResponseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/api-response.php', 'api-response');
    }

    public function boot(): void
    {
        $this->app->bind('api-response', config('api-response.bind_class'));

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'api-response');

        if (App::runningInConsole()) {
            //
            $this->publishes([
                __DIR__.'/../config/' => base_path('config'),
            ], 'api-response-config');

            $this->publishes([
                __DIR__.'/../lang/' => base_path('lang/vendor/api-response'),
            ], 'api-response-lang');

            $this->publishes([
                __DIR__.'/../stub/' => base_path('lang/vendor/api-response'),
            ], 'api-response-lang');
            //
        }
    }
}
