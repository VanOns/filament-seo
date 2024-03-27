<?php

namespace VanOns\FilamentSeo\Maps;

use VanOns\FilamentSeo\Concerns\HasSeoGetters;
use VanOns\FilamentSeo\Contracts\Seoable;

class FallbackMap implements Seoable
{
    use HasSeoGetters;

    public function getSeoTitle(): array
    {
        return [];
    }

    public function getSeoCanonical(): ?string
    {
        $url = request()->url();

        $params = collect(config('seo.canonical.parameters'))->mapWithKeys(
            fn($p) => [$p => request($p)]
        );

        $pagination = collect(config('seo.canonical.pagination_parameters'))->mapWithKeys(
            fn($p) => [$p => request($p) === '1' ? null : request($p)]
        );

        $query = http_build_query($params->merge($pagination)->toArray());

        return $query ? $url . '?' . $query : $url;
    }

    public function url(): string
    {
        return request()->url();
    }
}
