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

    public function show(string $sitemap, ?string $page = null): Response
    {
        $sitemap = SitemapRegistry::getSitemap($sitemap);

        abort_if(!$sitemap, 404);

        if ($page === null) {
            abort_if($sitemap->pages() > 1, 404);
        } else {
            $page = filter_var($page, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => $sitemap->pages()],
            ]);

            abort_if($page === false, 404);
        }

        return response()->view('filament-seo::sitemap.show', [
            'sitemap' => $sitemap,
            'page' => $page ?? 1,
        ])->header('Content-Type', 'text/xml');
    }
}
