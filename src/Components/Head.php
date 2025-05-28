<?php

namespace VanOns\FilamentSeo\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use VanOns\FilamentSeo\SeoService;

class Head extends Component
{

    public function render(): View
    {
        return view('filament-seo::components.head', [
            'service' => app(SeoService::class),
        ]);
    }
}
