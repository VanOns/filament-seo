<?php

namespace Tests\Fixtures;

use VanOns\FilamentSeo\Concerns\HasSeoGetters;
use VanOns\FilamentSeo\Contracts\Seoable;

class FakeSeoable implements Seoable
{
    use HasSeoGetters;

    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $content = null,
        public ?string $focusKeyphrase = null,
        public ?string $urlValue = null,
    ) {
    }

    public function getSeoTitle(): array
    {
        return $this->title !== null ? [$this->title] : [];
    }

    public function url(): ?string
    {
        return $this->urlValue;
    }

    public function getFocusKeyphrase(): ?string
    {
        return $this->focusKeyphrase;
    }

    public function getSeoDescription(): ?string
    {
        return $this->description;
    }

    public function getSeoContent(): ?string
    {
        return $this->content;
    }
}
