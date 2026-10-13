<?php

use VanOns\FilamentSeo\Analysis\Analysers\Canonical;
use VanOns\FilamentSeo\Analysis\Analysers\ContentLength;
use VanOns\FilamentSeo\Analysis\Analysers\DescriptionLength;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInContent;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInDescription;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInTitle;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInUrl;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseLength;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyPhraseUnique;
use VanOns\FilamentSeo\Analysis\Analysers\Indexable;
use VanOns\FilamentSeo\Analysis\Analysers\Sitemap;
use VanOns\FilamentSeo\Analysis\Analysers\TitleLength;

return [

    'settings' => [
        'driver' => 'config', // config, database
        'config' => [
            'title' => env('APP_NAME'),
            'title_separator' => '-',
            'enable_suffix' => true,
            'og_image' => null,
            'noindex' => false,
        ],
    ],

    'fallback' => \VanOns\FilamentSeo\Maps\FallbackMap::class,

    /*
    |--------------------------------------------------------------------------
    | Canonical Settings
    |--------------------------------------------------------------------------
    |
    | This option controls the settings for Canonical URL generation.
    |
    | 'parameters'
    | The parameters to include in the Canonical URL
    |
    | 'pagination_parameters'
    | The pagination parameters to include in the Canonical URL
    | This also handles some edge cases that are relevant for pagination
    |
    */
    'canonical' => [
        'parameters' => [],
        'pagination_parameters' => ['page'],
    ],

    /*
    |--------------------------------------------------------------------------
    | No-Index Settings
    |--------------------------------------------------------------------------
    |
    | Toggle search engine indexing.
    |
    */
    'no-index' => env('APP_SEO_NO_INDEX', false),

    /*
    |--------------------------------------------------------------------------
    | Analysis Settings
    |--------------------------------------------------------------------------
    |
    | Set the default SEO analysis settings.
    |
    */
    'analysis' => [
        'default_analysers' => [
            TitleLength::class,
            DescriptionLength::class,
            ContentLength::class,
            FocusKeyphraseLength::class,
            FocusKeyphraseInContent::class,
            FocusKeyphraseInTitle::class,
            FocusKeyphraseInDescription::class,
            FocusKeyphraseInUrl::class,
            FocusKeyPhraseUnique::class,
            Indexable::class,
            Canonical::class,
            Sitemap::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Settings
    |--------------------------------------------------------------------------
    |
    | Enable AI features for generating SEO metadata.
    | Make sure to set the appropriate environment variables including the API keys.
    | PrismPHP is used for AI integration, see https://prismphp.com/getting-started/configuration.html for more details.
    |
    */
    'ai' => [
        'enabled' => env('SEO_AI_ENABLED', false),
        'provider' => env('SEO_AI_PROVIDER', 'openai'),
        'model' => env('SEO_AI_MODEL', 'gpt-4o'),
        'rate_limit' => env('SEO_AI_RATE_LIMIT', 60),
        'rate_limit_decay_seconds' => env('SEO_AI_RATE_LIMIT_DECAY_SECONDS', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analysis Settings
    |--------------------------------------------------------------------------
    |
    | Set the default SEO analysis settings.
    |
    */
    'sitemap' => [
        'routes_enabled' => env('SEO_SITEMAP_ENABLED', true),

        // Keep instances that are set to noindex out of the sitemap.
        'exclude_noindex' => env('SEO_SITEMAP_EXCLUDE_NOINDEX', true),
    ],
];
