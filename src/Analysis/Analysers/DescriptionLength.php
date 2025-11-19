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
        $title = $record->getSeoDescription() ?? null;
        $length = strlen($title);
        $percentage = max($length / $this->maxRecommendedLength * 100, 0);

        return match(true) {
            $percentage === 0 => new AnalysisResult(AnalysisStatus::FAILED, __('The description is missing')),
            $percentage > 0 && $percentage <= 100 => new AnalysisResult(AnalysisStatus::PASSED, __('The description length is correct')),
            $percentage > 100 && $percentage <= 120 => new AnalysisResult(AnalysisStatus::WARNING, __('The description length is a bit too long')),
            default => new AnalysisResult(AnalysisStatus::FAILED, __('The description length is too long')),
        };
    }
}
