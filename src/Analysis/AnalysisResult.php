<?php

namespace VanOns\FilamentSeo\Analysis;

class AnalysisResult
{
    public function __construct(
        public AnalysisStatus $status,
        public string $message
    ) {
    }
}
