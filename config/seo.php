<?php

use VanOns\FilamentSeo\Analysis\Analysers\ContentLength;
use VanOns\FilamentSeo\Analysis\Analysers\DescriptionLength;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInContent;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInDescription;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInTitle;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInUrl;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseLength;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyPhraseUnique;
use VanOns\FilamentSeo\Analysis\Analysers\TitleLength;

return [

    'settings' => [
        'driver' => 'config', // config, database
        'config' => [
            'title' => env('APP_NAME'),
            'title_separator' => '-',
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
        'cache_ttl' => 3600,
    ],
];
