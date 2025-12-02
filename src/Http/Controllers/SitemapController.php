<?php

namespace VanOns\FilamentSeo\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VanOns\FilamentSeo\Facades\SitemapRegistry;

class SitemapController extends Controller
{
    public function index(): Response
    {
        return response()->view('filament-seo::sitemap.index', [
            'sitemaps' => SitemapRegistry::getSitemaps(),
        ])->header('Content-Type', 'text/xml');
    }

    public function show(string $sitemap, ?int $page = null): Response
    {
        $sitemap = SitemapRegistry::getSitemap($sitemap);

        abort_if(!$sitemap, 404);
        abort_if($page === null && $sitemap->pages() > 1, 404);

        return response()->view('filament-seo::sitemap.show', [
            'sitemap' => $sitemap,
            'page' => $page ?? 1,
        ])->header('Content-Type', 'text/xml');
    }
}
