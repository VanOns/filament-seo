<?php

use VanOns\FilamentSeo\Http\Controllers\SitemapController;

// Without the explicit pattern a name stops at the first `-`, and `establishment-pages-sitemap.xml` falls through.
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('{sitemap}-sitemap.xml', [SitemapController::class, 'show'])
    ->where('sitemap', '[^/]+')
    ->name('sitemap.show');
Route::get('{sitemap}-sitemap-{page}.xml', [SitemapController::class, 'show'])
    ->where('sitemap', '[^/]+')
    ->whereNumber('page')
    ->name('sitemap.page.show');
