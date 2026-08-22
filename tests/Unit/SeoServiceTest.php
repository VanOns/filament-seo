<?php

use Tests\Fixtures\FakeSeoable;
use VanOns\FilamentSeo\Contracts\SeoSettings;
use VanOns\FilamentSeo\SeoableRepository;
use VanOns\FilamentSeo\SeoImage;
use VanOns\FilamentSeo\SeoService;
use VanOns\FilamentSeo\Settings\ConfigSettingsDriver;

function makeService(?SeoSettings $settings = null): SeoService
{
    $settings ??= new ConfigSettingsDriver();
    return new SeoService($settings, app(SeoableRepository::class));
}

it('data includes title when seoable has one', function () {
    $service = makeService();
    $service->setSeoable(new FakeSeoable(title: 'My Page'));

    expect($service->data())->toHaveKey('title')
        ->and($service->data()['title'])->toBe(['My Page']);
});

it('data excludes title when seoable has none', function () {
    $service = makeService();
    $service->setSeoable(new FakeSeoable());

    expect($service->data())->not->toHaveKey('title');
});

it('data includes description when seoable has one', function () {
    $service = makeService();
    $service->setSeoable(new FakeSeoable(description: 'A description'));

    expect($service->data())->toHaveKey('description');
});

it('data merges additional data', function () {
    $service = makeService();
    $service->setSeoable(new FakeSeoable());
    $service->merge(['custom' => 'value']);

    expect($service->data())->toHaveKey('custom')
        ->and($service->data()['custom'])->toBe('value');
});

it('title joins parts with separator and appends the site title', function () {
    config([
        'seo.settings.config.title' => 'My Site',
        'seo.settings.config.title_separator' => '-',
    ]);

    $service = makeService();
    $service->setSeoable(new FakeSeoable(title: 'My Page'));

    expect($service->title())->toBe('My Page - My Site');
});

it('title returns just the site title when seoable has no title parts', function () {
    config([
        'seo.settings.config.title' => 'My Site',
        'seo.settings.config.title_separator' => '-',
    ]);

    $service = makeService();
    $service->setSeoable(new FakeSeoable());

    expect($service->title())->toBe('My Site');
});

it('getSeoRobots returns noindex when settings force it', function () {
    config(['seo.settings.config.noindex' => true]);

    $service = makeService();
    $service->setSeoable(new FakeSeoable());

    expect($service->getSeoRobots())->toBe('noindex');
});

it('getSeoRobots delegates to seoable when noindex is off', function () {
    config(['seo.settings.config.noindex' => false]);

    $service = makeService();
    $service->setSeoable(new FakeSeoable());

    expect($service->getSeoRobots())->toBeNull();
});

it('data includes og image fields when seoable provides an image', function () {
    $seoable = new class () extends FakeSeoable {
        public function getSeoOgImage(): ?SeoImage
        {
            return new SeoImage('https://example.com/og.jpg', 1200, 630, 'image/jpeg');
        }
    };

    $service = makeService();
    $service->setSeoable($seoable);

    $data = $service->data();
    expect($data)->toHaveKey('og:image')
        ->and($data['og:image'])->toBe('https://example.com/og.jpg')
        ->and($data['og:image:type'])->toBe('image/jpeg')
        ->and($data['og:image:width'])->toBe(1200)
        ->and($data['og:image:height'])->toBe(630);
});

it('metaTags returns an array with expected meta entries', function () {
    $service = makeService();
    $service->setSeoable(new FakeSeoable(description: 'My description'));

    $keys = collect($service->metaTags())->pluck('name')->filter()->values()->all();
    expect($keys)->toContain('description');
});

it('canonical returns the seoable canonical url', function () {
    $seoable = new class () extends FakeSeoable {
        public function getSeoCanonical(): ?string
        {
            return 'https://example.com/page';
        }
    };

    $service = makeService();
    $service->setSeoable($seoable);

    expect($service->canonical())->toBe('https://example.com/page');
});
