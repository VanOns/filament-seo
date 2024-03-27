@php
/**
 * @var \VanOns\FilamentSeo\SeoService $service
 */
@endphp


<title>@yield('seo.title', $service->title())</title>

@if($ogTitle = \Illuminate\Support\Facades\View::getSection('seo.og:title', $service->ogTitle()))
    <meta property="og:title" content="{{ $ogTitle }}">
@endif

@if($twitterTitle = $service->twitterTitle())
    <meta property="twitter:title" content="{{ $twitterTitle }}">
@endif

@if($canonical = $service->canonical())
    <link rel="canonical" href="{{ $canonical }}">
@endif

{{ $service->renderMetaTags() }}
