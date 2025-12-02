<?php

namespace VanOns\FilamentSeo\Concerns;

use Illuminate\Support\Arr;
use VanOns\FilamentSeo\Facades\AnalyserRegistry;
use VanOns\FilamentSeo\FormSchemas\ResourceSchema;
use VanOns\FilamentSeo\SeoImage;
use VanOns\FilamentSeo\Sitemap\SitemapUrl;

trait HasSeoFields
{
    use HasSeoGetters;

    public static string $seoPrefix = 'seo.';

    public function getFocusKeyphrase(): ?string
    {
        return Arr::get($this, static::$seoPrefix . 'focus_keyphrase');
    }

    public static function getDefaultSeoTitleField(): string
    {
        return 'title';
    }

    public function getDefaultSeoTitle(): ?string
    {
        return $this->{static::getDefaultSeoTitleField()};
    }

    public function getSeoTitle(): array
    {
        return [Arr::get($this, static::$seoPrefix . 'title') ?: $this->getDefaultSeoTitle()];
    }

    public function getDefaultSeoDescription(): ?string
    {
        return null;
    }

    public function getSeoDescription(): ?string
    {
        return Arr::get($this, static::$seoPrefix . 'description') ?: $this->getDefaultSeoDescription();
    }

    public function getSeoKeywords(): ?string
    {
        return Arr::get($this, static::$seoPrefix . 'keywords');
    }

    public function getNoindex(): bool
    {
        return Arr::get($this, static::$seoPrefix . 'noindex', false);
    }

    public function getNofollow(): bool
    {
        return Arr::get($this, static::$seoPrefix . 'nofollow', false);
    }

    public function getSeoRobots(): ?string
    {
        return collect([
            $this->getNoindex() ? 'noindex' : null,
            $this->getNofollow() ? 'nofollow' : null,
        ])->filter()->implode(', ');
    }

    public function getSeoCanonical(): ?string
    {
        return match (Arr::get($this, static::$seoPrefix . 'canonical')) {
            'url' => Arr::get($this, static::$seoPrefix . 'canonical_url'),
            default => null,
        };
    }

    public function getDefaultSeoImage(): ?string
    {
        return $this->featured_image ?? null;
    }

    public function getSeoOgImage(): ?SeoImage
    {
        if (!$image = Arr::get($this, static::$seoPrefix . 'og_image')) {
            return null;
        }

        return new SeoImage(url($image));
    }

    public function getSeoOgTitle(): ?array
    {
        if ($title = Arr::get($this, static::$seoPrefix . 'og_title')) {
            return [$title];
        }

        return $this->getSeoTitle();
    }

    public function getSeoOgDescription(): ?string
    {
        return Arr::get($this, static::$seoPrefix . 'og_description') ?: $this->getSeoDescription();
    }

    public function getSeoChangeFrequency(): ?string
    {
        return Arr::get($this, static::$seoPrefix . 'change_frequency');
    }

    public function getSeoPriority(): ?float
    {
        $priority = Arr::get($this, static::$seoPrefix . 'priority');
        return $priority ? floatval($priority) : null;
    }

    public function getSeoLastModified(): ?string
    {
        $column = defined(static::class . '::UPDATED_AT') ? static::UPDATED_AT : 'updated_at';
        return Arr::get($this, $column)?->format('Y-m-d');
    }

    public function toSitemapUrl(): ?SitemapUrl
    {
        $url = $this->url();

        if (!$url) {
            return null;
        }

        return new SitemapUrl(
            loc: $this->url(),
            lastmod: $this->getSeoLastModified(),
            changefreq: $this->getSeoChangeFrequency(),
            priority: $this->getSeoPriority(),
        );
    }

    public function getSeoAnalysers(): array
    {
        return AnalyserRegistry::getAnalysers($this);
    }

    public static function getSeoFields(): array
    {
        return app(ResourceSchema::class)->schema();
    }
}
