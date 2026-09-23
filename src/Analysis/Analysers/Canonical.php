<?php

namespace VanOns\FilamentSeo\Analysis\Analysers;

use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

class Canonical implements AnalyserInterface
{
    public function analyse(Seoable $record): ?AnalysisResult
    {
        $canonical = $record->getSeoCanonical();

        return match (true) {
            empty($canonical) => null,
            $this->urlsMatch($canonical, $record->url()) => new AnalysisResult(
                AnalysisStatus::PASSED,
                __('filament-seo::seo.canonical.self')
            ),
            default => new AnalysisResult(
                AnalysisStatus::INFO,
                __('filament-seo::seo.canonical.points_elsewhere', ['url' => $canonical])
            ),
        };
    }

    protected function urlsMatch(?string $a, ?string $b): bool
    {
        if (!$a || !$b) {
            return false;
        }

        return $this->normaliseUrl($a) === $this->normaliseUrl($b);
    }

    /**
     * Ignores scheme, host casing and trailing slashes so a hand-typed
     * canonical isn't reported as pointing somewhere else.
     */
    protected function normaliseUrl(string $url): string
    {
        $parts = parse_url($url);

        $host = strtolower($parts['host'] ?? '');
        $path = rtrim($parts['path'] ?? '', '/');
        $query = $parts['query'] ?? null;

        return $host . $path . ($query ? '?' . $query : '');
    }
}
