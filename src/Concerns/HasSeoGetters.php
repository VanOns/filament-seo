<?php

namespace VanOns\FilamentSeo\Concerns;

use VanOns\FilamentSeo\SeoImage;

trait HasSeoGetters
{
    public function getUrl(): ?string
    {
        return null;
    }

    public function getFocusKeyphrase(): ?string
    {
        return null;
    }

    public function getSeoDescription(): ?string
    {
        return null;
    }

    public function getSeoKeywords(): ?string
    {
        return null;
    }

    public function getSeoRobots(): ?string
    {
        return null;
    }

    public function getSeoCanonical(): ?string
    {
        return null;
    }

    public function getSeoOgImage(): ?SeoImage
    {
        return null;
    }

    public function getSeoOgTitle(): ?array
    {
        return $this->getSeoTitle();
    }

    public function getSeoOgDescription(): ?string
    {
        return $this->getSeoDescription();
    }


    public function getSeoTwitterTitle(): ?array
    {
        return $this->getSeoOgTitle();
    }

    public function getSeoTwitterDescription(): ?string
    {
        return $this->getSeoOgDescription();
    }

    public function getSeoTwitterImage(): ?SeoImage
    {
        return $this->getSeoOgImage();
    }

    public function getSeoTwitterCard(): ?string
    {
        return 'summary_large_image';
    }

    public function getSeoChangeFrequency(): ?string
    {
        return null;
    }

    public function getSeoPriority(): ?string
    {
        return null;
    }

    public function getSeoLastModified(): ?string
    {
        return null;
    }

    public function getSeoContent(): ?string
    {
        return null;
    }

    public function getSeoAnalysers(): array
    {
        return [];
    }
}
