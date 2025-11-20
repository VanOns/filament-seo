<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use Illuminate\Support\Str;
use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class FocusKeyphraseInUrl implements AnalyserInterface
{
    public function analyse(Seoable $record): ?AnalysisResult
    {
        $url = $record->url();
        $focusKeyphrase = Str::slug($record->getFocusKeyphrase() ?? null);

        return match (true) {
            empty($url) => null,
            $focusKeyphrase && str_contains($url, $focusKeyphrase) => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.focus_keyphrase.in_url')
            ),
            $focusKeyphrase && $this->allWordsPresent($url, $focusKeyphrase) => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.focus_keyphrase.words_in_url')
            ),
            default => new AnalysisResult(
                AnalysisStatus::FAILED,
                __('filament-seo::seo.focus_keyphrase.not_in_url')
            ),
        };
    }

    public function allWordsPresent(string $url, string $focusKeyphraseSlug): bool
    {
        $words = explode('-', $focusKeyphraseSlug);

        foreach ($words as $word) {
            if (!str_contains($url, $word)) {
                return false;
            }
        }

        return true;
    }
}
