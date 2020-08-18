<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\NovaConfig;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Illuminatech\NovaConfig\Http\Middleware\Authorize;

/**
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.1.5
 */
class NovaConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/lang/' => resource_path('lang/vendor/illuminatech/nova-config'),
            ]);
        }

        $this->loadTranslations();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nova-config');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            $this->bootTranslations();
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
            ->prefix('illuminatech/nova-config')
            ->name('illuminatech.nova-config.')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Load package translation resources.
     *
     * @return void
     */
    protected function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'illuminatech/nova-config');
        $this->loadJSONTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadJSONTranslationsFrom(resource_path('lang/vendor/illuminatech/nova-config'));
    }

    /**
     * Bootstraps current application locale translations to Nova.
     *
     * @return void
     */
    protected function bootTranslations()
    {
        $currentLocale = $this->app->getLocale();

        Nova::translations(__DIR__.'/../resources/lang/'.$currentLocale.'.json');
        Nova::translations(resource_path('lang/vendor/illuminatech/nova-config/'.$currentLocale.'.json'));
    }
}
