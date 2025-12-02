<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use Illuminate\Support\Str;
use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class FocusKeyphraseInTitle implements AnalyserInterface
{
    public function analyse(Seoable $record): ?AnalysisResult
    {
        $title = Str::lower($record->getSeoTitle()[0] ?? null);
        $focusKeyphrase = Str::lower($record->getFocusKeyphrase() ?? null);

        return match (true) {
            $focusKeyphrase && str_contains($title, $focusKeyphrase) => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.focus_keyphrase.in_title')
            ),
            $focusKeyphrase && $this->allWordsPresent($title, $focusKeyphrase) => new AnalysisResult(
                AnalysisStatus::WARNING,
                __('filament-seo::seo.focus_keyphrase.words_in_title')
            ),
            default => new AnalysisResult(
                AnalysisStatus::FAILED,
                __('filament-seo::seo.focus_keyphrase.not_in_title')
            ),
        };
    }

    public function allWordsPresent(string $title, string $focusKeyphrase): bool
    {
        $words = explode(' ', $focusKeyphrase);

        foreach ($words as $word) {
            if (!str_contains($title, $word)) {
                return false;
            }
        }

        return true;
    }
}
