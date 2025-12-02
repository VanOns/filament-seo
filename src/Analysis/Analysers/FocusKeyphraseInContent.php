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
        $paragraphs = Str::of($content)->replace("\n\n", "\n")->explode("\n");
        $focusKeyphrase = Str::lower($record->getFocusKeyphrase() ?? null);

        $count = $paragraphs->map(fn (string $paragraph) => $this->countPresence($paragraph, $focusKeyphrase))->sum();
        $words = Str::wordCount($content);

        $min = match(true) {
            $words <= 99 => 1,
            default => round(0.005 * $words)
        };

        $max = match(true) {
            $words <= 50 => 1,
            $words <= 99 => 2,
            default => round(0.03 * $words)
        };

        return match (true) {
            !$content => null,
            !$focusKeyphrase || $count === 0 => new AnalysisResult(
                AnalysisStatus::FAILED,
                __('filament-seo::seo.focus_keyphrase.not_in_content')
            ),
            $count < $min => new AnalysisResult(
                AnalysisStatus::FAILED,
                __('filament-seo::seo.focus_keyphrase.not_enough_in_content', ['count' => $count, 'recommended' => $min])
            ),
            $count > $max => new AnalysisResult(
                AnalysisStatus::FAILED,
                __('filament-seo::seo.focus_keyphrase.too_much_in_content', ['count' => $count, 'recommended' => $max])
            ),
            default => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.focus_keyphrase.in_content', ['count' => $count])
            )
        };
    }

    public function countPresence(string $paragraph, string $focusKeyphrase): int
    {
        return collect(explode(' ', $focusKeyphrase))->map(function (string $word) use ($paragraph) {
            $words = str_word_count($paragraph, 1);
            return collect($words)->filter(fn (string $w) => Str::lower($w) === Str::lower($word))->count();
        })->min();
    }
}
