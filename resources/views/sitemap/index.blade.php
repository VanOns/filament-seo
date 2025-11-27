@php
    /**
     * @var \Illuminate\Support\Collection<\VanOns\FilamentSeo\Sitemap\Sitemap> $sitemaps
     */
    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
@endphp
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($sitemaps as $sitemap)
        @for($page = 1; $page <= $sitemap->pages(); $page++)
            <sitemap>
                <loc>{{ $sitemap->url($page) }}</loc>
            </sitemap>
        @endfor
    @endforeach
</sitemapindex>
