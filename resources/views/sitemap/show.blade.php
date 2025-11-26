@php
    /**
    * @var \VanOns\FilamentSeo\Sitemap\Sitemap $sitemap
    */
    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($sitemap->urls() as $url)
        <url>
            <loc>{{ $url->loc }}</loc>
            @if($url->lastmod)
                <lastmod>{{ $url->lastmod }}</lastmod>
            @endif
            @if($url->changefreq)
                <changefreq>{{ $url->changefreq }}</changefreq>
            @endif
            @if($url->priority)
                <priority>{{ $url->priority }}</priority>
            @endif
        </url>
    @endforeach
</urlset>
