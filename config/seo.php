<?php

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
    |  The pagination parameters to include in the Canonical URL
    |  This also handles some edge cases that are relevant for pagination
    |
    */
    'canonical' => [
        'parameters' => [],
        'pagination_parameters' => ['page'],
    ],
    /**
     * Toggle search engine indexing
     */
    'indexable' => env('APP_SEO_INDEXABLE', false),
];
