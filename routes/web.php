<?php

use VanOns\FilamentSeo\Http\Controllers\SitemapController;

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('{sitemap}-sitemap.xml', [SitemapController::class, 'show'])->name('sitemap.show');
Route::get('{sitemap}-sitemap-{page}.xml', [SitemapController::class, 'show'])
    ->whereNumber('page')
    ->name('sitemap.page.show');
