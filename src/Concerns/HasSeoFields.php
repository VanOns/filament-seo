<?php

namespace VanOns\FilamentSeo\Concerns;

use Illuminate\Support\Arr;
use VanOns\FilamentSeo\FormSchemas\ResourceSchema;
use VanOns\FilamentSeo\SeoImage;

trait HasSeoFields
{
    use HasSeoGetters;

    public static string $seoPrefix = 'seo.';

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
        return [Arr::get($this, static::$seoPrefix . 'title') ?? $this->getDefaultSeoTitle()];
    }

    public function getDefaultSeoDescription(): ?string
    {
        return null;
    }

    public function getSeoDescription(): ?string
    {
        return Arr::get($this, static::$seoPrefix . 'description') ?? $this->getDefaultSeoDescription();
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
        return match(Arr::get($this, static::$seoPrefix . 'canonical')) {
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
        return Arr::get($this, static::$seoPrefix . 'og_description') ?? $this->getSeoDescription();
    }

    public function getSeoChangeFrequency(): ?string
    {
        return Arr::get($this, static::$seoPrefix . 'change_frequency');
    }

    public function getSeoPriority(): ?string
    {
        return Arr::get($this, static::$seoPrefix . 'priority');
    }

    public function getSeoLastModified(): ?string
    {
        return Arr::get($this, 'updated_at')?->format('Y-m-d');
    }

    public static function getSeoFields(): array
    {
        return app(ResourceSchema::class)->schema();
    }
}
