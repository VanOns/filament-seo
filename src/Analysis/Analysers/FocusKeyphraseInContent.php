<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use Illuminate\Support\Str;
use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class FocusKeyphraseInContent implements AnalyserInterface
{
    public function analyse(Seoable $record): ?AnalysisResult
    {
        $content = Str::lower($record->getSeoContent() ?? null);
        $focusKeyphrase = Str::lower($record->getFocusKeyphrase() ?? null);

        return match (true) {
            $content === null => null,
            $focusKeyphrase && str_contains($content, $focusKeyphrase) => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.focus_keyphrase.in_content')
            ),
            $focusKeyphrase && $this->allWordsPresent($content, $focusKeyphrase) => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.focus_keyphrase.words_in_content')
            ),
            default => new AnalysisResult(
                AnalysisStatus::FAILED,
                __('filament-seo::seo.focus_keyphrase.not_in_content')
            ),
        };
    }

    public function allWordsPresent(string $content, string $focusKeyphrase): bool
    {
        $words = explode(' ', $focusKeyphrase);

        foreach ($words as $word) {
            if (!str_contains($content, $word)) {
                return false;
            }
        }

        return true;
    }
}
