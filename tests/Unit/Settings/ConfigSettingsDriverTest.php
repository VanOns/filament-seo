<?php

use VanOns\FilamentSeo\Settings\ConfigSettingsDriver;

it('reads the site title from config', function () {
    config(['seo.settings.config.title' => 'My Site']);
    expect((new ConfigSettingsDriver())->title())->toBe('My Site');
});

it('reads the title separator from config', function () {
    config(['seo.settings.config.title_separator' => '-']);
    expect((new ConfigSettingsDriver())->titleSeparator())->toBe('-');
});

it('returns the og image from config', function () {
    config(['seo.settings.config.og_image' => 'https://example.com/og.png']);
    expect((new ConfigSettingsDriver())->ogImage())->toBe('https://example.com/og.png');
});

it('returns null for og image when not configured', function () {
    config(['seo.settings.config.og_image' => null]);
    expect((new ConfigSettingsDriver())->ogImage())->toBeNull();
});

it('returns noindex false by default', function () {
    config(['seo.settings.config.noindex' => false]);
    expect((new ConfigSettingsDriver())->noindex())->toBeFalse();
});

it('returns noindex true when configured', function () {
    config(['seo.settings.config.noindex' => true]);
    expect((new ConfigSettingsDriver())->noindex())->toBeTrue();
});
