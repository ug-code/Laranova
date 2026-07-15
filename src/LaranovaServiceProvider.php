<?php

namespace Laranova;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Laranova\Services\ApiClient;
use Laranova\Services\RouteScanner;
use Laranova\Services\ScriptParser;

class LaranovaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!$this->app->environment('local')) {
            return;
        }

        $this->registerRoutes();
        $this->registerViews();
        $this->registerPublishing();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laranova.php',
            'laranova'
        );

        $this->registerBindings();
    }

    protected function registerBindings(): void
    {
        $this->app->singleton(ApiClient::class, fn($app): ApiClient => new ApiClient(
            timeout: (int) config('laranova.http.timeout', 30),
            connectTimeout: (int) config('laranova.http.connect_timeout', 10),
        ));

        $this->app->singleton(ScriptParser::class, fn($app): ScriptParser => new ScriptParser(
            locale: (string) config('laranova.faker.locale', 'en_US'),
        ));

        $this->app->singleton(RouteScanner::class);
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => 'laranova',
            'middleware' => ['web'],
            'as' => 'laranova.',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        })->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laranova');
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laranova.php' => config_path('laranova.php'),
            ], 'laranova-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/laranova'),
            ], 'laranova-views');
        }
    }
}
