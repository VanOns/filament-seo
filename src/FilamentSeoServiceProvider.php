<?php

namespace VanOns\FilamentSeo;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Blade;
use VanOns\FilamentSeo\Contracts\ResourceSchema as ResourceSchemaContract;
use VanOns\FilamentSeo\Contracts\SeoSettings;
use VanOns\FilamentSeo\FormSchemas\ResourceSchema;
use VanOns\FilamentSeo\Http\Middleware\NoIndex;
use VanOns\FilamentSeo\Settings\ConfigSettingsDriver;

class FilamentSeoServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerComponents();
        $this->registerGlobalMiddleware();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/seo.php', 'seo');

        $this->app->singleton(SeoService::class);
        $this->app->bind(ResourceSchemaContract::class, ResourceSchema::class);

        if (config('seo.settings.driver') === 'config') {
            $this->app->bind(SeoSettings::class, ConfigSettingsDriver::class);
        }
    }

    public function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/seo.php' => config_path('seo.php'),
        ], 'config');
    }

    public function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-seo');
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-seo'),
        ], 'views');
    }

    public function registerComponents(): void
    {
        Blade::componentNamespace('VanOns\\FilamentSeo\\Components', 'filament-seo');
    }

    public function registerGlobalMiddleware(): void
    {
        $kernel = app()->make(Kernel::class);

        $kernel->pushMiddleware(NoIndex::class);
    }
}
