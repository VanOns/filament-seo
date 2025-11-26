<?php

use VanOns\FilamentSeo\Http\Controllers\SitemapController;

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('{sitemap}-sitemap.xml', [SitemapController::class, 'show'])->name('sitemap.show');
