<?php

namespace VanOns\FilamentSeo\Contracts;

interface SeoSettings
{
    public function title(): string;

    public function titleSeparator(): string;

    public function suffixEnabled(): bool;

    public function getSuffix(): ?string;

    public function ogImage(): ?string;

    public function noindex(): bool;
}
