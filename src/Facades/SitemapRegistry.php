<?php

namespace VanOns\FilamentSeo\Facades;

use Illuminate\Support\Facades\Facade;

class SitemapRegistry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \VanOns\FilamentSeo\Sitemap\SitemapRegistry::class;
    }
}
