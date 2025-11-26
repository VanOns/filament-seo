@php
    /**
     * @var \Illuminate\Support\Collection<\VanOns\FilamentSeo\Sitemap\Sitemap> $sitemaps
     */
    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
@endphp
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($sitemaps as $sitemap)
        <sitemap>
            <loc>{{ $sitemap->url() }}</loc>
        </sitemap>
    @endforeach
</sitemapindex>
