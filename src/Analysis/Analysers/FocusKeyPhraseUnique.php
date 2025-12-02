<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class FocusKeyPhraseUnique implements AnalyserInterface
{
    public function __construct(public string $column = 'seo.focus_keyphrase', public ?Closure $uniqueQuery = null)
    {
    }

    public function analyse(Seoable $record): ?AnalysisResult
    {
        $focusKeyphrase = $record->getFocusKeyphrase();

        if (!$record instanceof Model) {
            return null;
        }

        $query = $record::query()
            ->where(Str::replace('.', '->', $this->column), $focusKeyphrase)
            ->whereNot($record->getKeyName(), $record->getKey());

        if ($this->uniqueQuery) {
            $query = ($this->uniqueQuery)($query, $record);
        }

        if ($query->exists()) {
            return new AnalysisResult(
                AnalysisStatus::FAILED,
                __('filament-seo::seo.focus_keyphrase.not_unique')
            );
        }

        return new AnalysisResult(
            AnalysisStatus::PASSED,
            __('filament-seo::seo.focus_keyphrase.unique')
        );
    }
}
