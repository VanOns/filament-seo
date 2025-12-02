<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use Illuminate\Support\Str;
use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class ContentLength implements AnalyserInterface
{
    public function __construct(public int $maxRecommendedLength = 160)
    {
    }

    public function analyse(Seoable $record): ?AnalysisResult
    {
        $content = $record->getSeoContent();
        $length = Str::wordCount($content);
        $recommended = 300;

        return match(true) {
            !$content => null,
            $length < 200 => new AnalysisResult(AnalysisStatus::FAILED, __('filament-seo::seo.content.length_too_short', ['recommended' => $recommended])),
            $length < $recommended => new AnalysisResult(AnalysisStatus::WARNING, __('filament-seo::seo.content.length_too_short', ['recommended' => $recommended])),
            default => new AnalysisResult(AnalysisStatus::PASSED, __('filament-seo::seo.content.length_correct', ['recommended' => $recommended])),
        };
    }
}
