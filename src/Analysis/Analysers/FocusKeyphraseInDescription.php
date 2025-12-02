<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use Illuminate\Support\Str;
use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class FocusKeyphraseInDescription implements AnalyserInterface
{
    public function analyse(Seoable $record): ?AnalysisResult
    {
        $description = Str::lower($record->getSeoDescription() ?? null);
        $focusKeyphrase = Str::lower($record->getFocusKeyphrase() ?? null);

        return match (true) {
            $focusKeyphrase && str_contains($description, $focusKeyphrase) => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.focus_keyphrase.in_description')
            ),
            $focusKeyphrase && $this->allWordsPresent($description, $focusKeyphrase) => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.focus_keyphrase.words_in_description')
            ),
            default => new AnalysisResult(
                AnalysisStatus::FAILED,
                __('filament-seo::seo.focus_keyphrase.not_in_description')
            ),
        };
    }

    public function allWordsPresent(string $description, string $focusKeyphrase): bool
    {
        $words = explode(' ', $focusKeyphrase);

        return collect($words)->every(function ($word) use ($description) {
            return str_contains($description, $word);
        });
    }
}
