<?php

namespace VanOns\FilamentSeo\Contracts;

use VanOns\FilamentSeo\Sitemap\SitemapUrl;

interface Sitemapable
{
    public function toSitemapUrl(): ?SitemapUrl;
}
