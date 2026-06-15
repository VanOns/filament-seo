<?php

use VanOns\FilamentSeo\Settings\ConfigSettingsDriver;

it('reads the site title from config', function () {
    config(['seo.settings.config.title' => 'My Site']);
    expect((new ConfigSettingsDriver())->title())->toBe('My Site');
});

it('wraps the separator in spaces', function () {
    config(['seo.settings.config.title_separator' => '-']);
    expect((new ConfigSettingsDriver())->titleSeparator())->toBe(' - ');
});

it('returns suffix disabled when configured', function () {
    config(['seo.settings.config.enable_suffix' => false]);
    expect((new ConfigSettingsDriver())->suffixEnabled())->toBeFalse();
});

it('returns suffix enabled when configured', function () {
    config(['seo.settings.config.enable_suffix' => true]);
    expect((new ConfigSettingsDriver())->suffixEnabled())->toBeTrue();
});

it('returns null suffix when suffix is disabled', function () {
    config(['seo.settings.config.enable_suffix' => false]);
    expect((new ConfigSettingsDriver())->getSuffix())->toBeNull();
});

it('returns separator plus title as suffix when enabled', function () {
    config([
        'seo.settings.config.enable_suffix' => true,
        'seo.settings.config.title' => 'My Site',
        'seo.settings.config.title_separator' => '-',
    ]);

    expect((new ConfigSettingsDriver())->getSuffix())->toBe(' - My Site');
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
