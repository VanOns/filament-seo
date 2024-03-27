<?php

namespace VanOns\FilamentSeo\Settings;

use VanOns\FilamentSeo\Contracts\SeoSettings;

class ConfigSettingsDriver implements SeoSettings
{
    public function title(): string
    {
        return config('seo.settings.config.title');
    }

    public function titleSeparator(): string
    {
        return config('seo.settings.config.title_separator');

    }

    public function ogImage(): ?string
    {
        return config('seo.settings.config.og_image');

    }

    public function noindex(): bool
    {
        return config('seo.settings.config.noindex');
    }
}
