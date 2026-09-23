<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;
use VanOns\FilamentSeo\Contracts\SeoSettings;

class Indexable implements AnalyserInterface
{
    public function analyse(Seoable $record): ?AnalysisResult
    {
        return match (true) {
            str_contains((string) $record->getSeoRobots(), 'noindex') => new AnalysisResult(
                AnalysisStatus::INFO,
                __('filament-seo::seo.indexable.no_index')
            ),
            !app()->isProduction() => new AnalysisResult(
                AnalysisStatus::INFO,
                __('filament-seo::seo.indexable.non_production')
            ),
            app(SeoSettings::class)->noindex(), config('seo.no-index', false) => new AnalysisResult(
                AnalysisStatus::WARNING,
                __('filament-seo::seo.indexable.site_no_index')
            ),
            default => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.indexable.indexable')
            ),
        };
    }
}
