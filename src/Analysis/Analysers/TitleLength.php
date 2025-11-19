<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class TitleLength implements AnalyserInterface
{
    public function __construct(public int $maxRecommendedLength = 70)
    {}

    public function analyse(Seoable $record): ?AnalysisResult
    {
        $title = $record->getSeoTitle()[0] ?? null;
        $length = strlen($title);
        $percentage = max($length / $this->maxRecommendedLength * 100, 0);

        return match(true) {
            $percentage === 0 => new AnalysisResult(AnalysisStatus::FAILED, __('The title is missing')),
            $percentage > 0 && $percentage <= 100 => new AnalysisResult(AnalysisStatus::PASSED, __('The title length is correct')),
            $percentage > 100 && $percentage <= 120 => new AnalysisResult(AnalysisStatus::WARNING, __('The title length is a bit too long')),
            default => new AnalysisResult(AnalysisStatus::FAILED, __('The title length is too long')),
        };
    }
}
