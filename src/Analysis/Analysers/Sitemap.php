<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class Sitemap implements AnalyserInterface
{
    public function analyse(Seoable $record): ?AnalysisResult
    {
        return match (true) {
            !config('seo.sitemap.routes_enabled', true) => null,
            empty($record->url()) => null,
            !$record->toSitemapUrl() => new AnalysisResult(
                AnalysisStatus::INFO,
                __('filament-seo::seo.sitemap_analysis.excluded')
            ),
            default => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.sitemap_analysis.included')
            ),
        };
    }
}
