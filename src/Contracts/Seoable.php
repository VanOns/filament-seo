<?php

namespace VanOns\FilamentSeo\Contracts;

use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\SeoImage;

interface Seoable extends HasUrl
{

    public function getFocusKeyphrase(): ?string;
    public function getSeoTitle(): array;

    public function getSeoDescription(): ?string;

    public function getSeoKeywords(): ?string;

    public function getSeoRobots(): ?string;

    public function getSeoCanonical(): ?string;

    public function getSeoOgTitle(): ?array;

    public function getSeoOgDescription(): ?string;

    public function getSeoOgImage(): ?SeoImage;

    public function getSeoTwitterTitle(): ?array;

    public function getSeoTwitterDescription(): ?string;

    public function getSeoTwitterImage(): ?SeoImage;

    public function getSeoTwitterCard(): ?string;

    public function getSeoChangeFrequency(): ?string;

    public function getSeoPriority(): ?string;

    public function getSeoLastModified(): ?string;

    public function getSeoContent(): ?string;

    /**
     * @return array<AnalyserInterface>
     */
    public function getSeoAnalysers(): array;
}
