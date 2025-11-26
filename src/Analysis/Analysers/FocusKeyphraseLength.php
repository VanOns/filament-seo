<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use Illuminate\Support\Str;
use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class FocusKeyphraseLength implements AnalyserInterface
{
    public function __construct(public int $words = 4)
    {
    }

    public function analyse(Seoable $record): ?AnalysisResult
    {
        $words = Str::wordCount($record->getFocusKeyphrase());

        return match(true) {
            $words === 0 => new AnalysisResult(AnalysisStatus::FAILED, __('filament-seo::seo.focus_keyphrase.missing')),
            $words > 0 && $words <= $this->words => new AnalysisResult(AnalysisStatus::PASSED, __('filament-seo::seo.focus_keyphrase.length_correct')),
            $words > $this->words && $words <= $this->words * 2 => new AnalysisResult(AnalysisStatus::WARNING, __('filament-seo::seo.focus_keyphrase.length_long', ['count' => $this->words])),
            default => new AnalysisResult(AnalysisStatus::FAILED, __('filament-seo::seo.focus_keyphrase.length_very_long', ['count' => $this->words])),
        };
    }
}
