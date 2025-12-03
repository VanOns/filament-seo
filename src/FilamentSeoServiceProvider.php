<?php

namespace VanOns\FilamentSeo;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use VanOns\FilamentSeo\Analysis\AnalyserRegistry;
use VanOns\FilamentSeo\Contracts\SeoSettings;
use VanOns\FilamentSeo\Http\Middleware\NoIndex;
use VanOns\FilamentSeo\Schemas\SeoFields;
use VanOns\FilamentSeo\Settings\ConfigSettingsDriver;
use VanOns\FilamentSeo\Sitemap\SitemapRegistry;

class FilamentSeoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerConfig();
        $this->registerRoute();
        $this->registerViews();
        $this->registerComponents();
        $this->registerGlobalMiddleware();
        $this->registerTranslations();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/seo.php', 'seo');

        $this->app->singleton(SeoService::class);
        $this->app->singleton(AnalyserRegistry::class);
        $this->app->singleton(SitemapRegistry::class);
        $this->app->bind(Contracts\SeoFields::class, SeoFields::class);

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

    public function registerRoute(): void
    {
        if (config('seo.sitemap.routes_enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }
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

    public function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-seo');
        $this->publishes([
            __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/filament-seo'),
        ], 'lang');
    }
}
