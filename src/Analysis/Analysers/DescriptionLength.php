<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class DescriptionLength implements AnalyserInterface
{
    public function __construct(public int $maxRecommendedLength = 160)
    {}

    public function analyse(Seoable $record): ?AnalysisResult
    {
        $description = $record->getSeoDescription() ?? null;
        $length = strlen($description);
        $percentage = max($length / $this->maxRecommendedLength * 100, 0);

        return match(true) {
            $percentage === 0 => new AnalysisResult(AnalysisStatus::FAILED, __('filament-seo::seo.description.missing')),
            $percentage > 0 && $percentage <= 100 => new AnalysisResult(AnalysisStatus::PASSED, __('filament-seo::seo.description.length_correct')),
            $percentage > 100 && $percentage <= 120 => new AnalysisResult(AnalysisStatus::WARNING, __('filament-seo::seo.description.length_bit_too_long')),
            default => new AnalysisResult(AnalysisStatus::FAILED, __('filament-seo::seo.description.length_too_long')),
        };
    }
}
