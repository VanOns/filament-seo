<?php

use VanOns\FilamentSeo\Sitemap\Sitemap;
use VanOns\FilamentSeo\Sitemap\SitemapUrl;

it('clamps perPage to at least one', function () {
    $sitemap = new Sitemap(slug: 'misc', urls: collect([new SitemapUrl('https://example.com')]), perPage: 0);

    expect($sitemap->perPage)->toBe(1)
        ->and($sitemap->pages())->toBe(1);
});

it('falls back to the default perPage when null is given', function () {
    $sitemap = new Sitemap(slug: 'misc', urls: collect([new SitemapUrl('https://example.com')]), perPage: null);

    expect($sitemap->perPage)->toBe(1000)
        ->and($sitemap->pages())->toBe(1);
});
