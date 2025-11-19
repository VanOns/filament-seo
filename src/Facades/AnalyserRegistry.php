<?php

namespace VanOns\FilamentSeo\Facades;

use Illuminate\Support\Facades\Facade;

class AnalyserRegistry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \VanOns\FilamentSeo\Analysis\AnalyserRegistry::class;
    }
}
