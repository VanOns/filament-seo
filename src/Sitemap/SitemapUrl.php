<?php

namespace VanOns\FilamentSeo\Sitemap;

use DateTimeInterface;

class SitemapUrl
{
    public function __construct(
        public string $loc,
        public ?string $lastmod = null,
        public ?string $changefreq = null,
        public ?float $priority = null,
    ) {
    }
}
