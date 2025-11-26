<?php

namespace VanOns\FilamentSeo\Sitemap;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use VanOns\FilamentSeo\Contracts\Seoable;

class Sitemap
{
    /**
     * @var Collection<SitemapUrl>
     */
    public Collection $urls;

    /**
     * @var Closure(): Collection<SitemapUrl>
     */
    public Closure $urlsCallback;

    public string $slug;

    /**
     * @param string $slug
     * @param Collection<SitemapUrl>|null $urls
     * @param (Closure(): Collection<SitemapUrl>)|null $urlsCallback
     */
    public function __construct(string $slug, ?Collection $urls = null, ?Closure $urlsCallback = null)
    {
        $this->slug = $slug;
        $this->urls = $urls ?? collect();
        $this->urlsCallback = $urlsCallback;
    }

    public function url(): string
    {
        return route('sitemap.show', ['sitemap' => $this->slug]);
    }

    /**
     * @return Collection<SitemapUrl>
     */
    public function urls(): Collection
    {
        if (isset($this->urlsCallback)) {
            $this->urls = ($this->urlsCallback)();
        }

        return $this->urls ?? collect();
    }

    public static function fromQuery(string $slug, Builder $query): Sitemap
    {
        return new static(
            slug: $slug,
            urlsCallback: fn () => $query->get()->map(
                fn (Seoable $seoable) => $seoable->getSeoSitemap()
            )
        );
    }

    public static function fromModel(string $slug, string $modelClass): Sitemap
    {
        return self::fromQuery(
            $slug,
            $modelClass::query()
        );
    }
}
