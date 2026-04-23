<?php

namespace VanOns\FilamentSeo\Settings;

use VanOns\FilamentSeo\Contracts\SeoSettings;

class ConfigSettingsDriver implements SeoSettings
{
    public function title(): string
    {
        return config('seo.settings.config.title', '');
    }

    public function titleSeparator(): string
    {
        $separator = trim(config('seo.settings.config.title_separator', ''));

        return " {$separator} ";
    }

    public function suffixEnabled(): bool
    {
        return config('seo.settings.config.enable_suffix', false);
    }

    public function getSuffix(): ?string
    {
        if (!$this->suffixEnabled()) {
            return null;
        }

        return $this->titleSeparator() . $this->title();
    }

    public function ogImage(): ?string
    {
        return config('seo.settings.config.og_image');
    }

    public function noindex(): bool
    {
        return config('seo.settings.config.noindex', false);
    }
}
