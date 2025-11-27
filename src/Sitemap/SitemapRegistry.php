<?php

namespace VanOns\FilamentSeo\Sitemap;

use Illuminate\Support\Collection;

class SitemapRegistry
{
    /**
     * @var Collection<Sitemap>
     */
    protected Collection $sitemaps;

    public function __construct()
    {
        $this->sitemaps = collect();
    }

    /**
     * @param Sitemap[] $sitemaps
     * @return void
     */
    public function register(array $sitemaps): void
    {
        $this->sitemaps = $this->sitemaps->merge($sitemaps);
    }

    /**
     * @return Collection<Sitemap>
     */
    public function getSitemaps(): Collection
    {
        return $this->sitemaps;
    }

    public function getSitemap(string $slug)
    {
        return $this->sitemaps->where('slug', $slug)->first();
    }
}
