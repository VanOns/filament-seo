<?php

namespace VanOns\FilamentSeo\Analysis;

use VanOns\FilamentSeo\Contracts\Seoable;

interface AnalyserInterface
{
    public function analyse(Seoable $record): ?AnalysisResult;
}
