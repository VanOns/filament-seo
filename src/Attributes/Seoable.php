<?php

namespace VanOns\FilamentSeo\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Seoable
{
    public function __construct(
        public string $model,
        public ?string $parameter = null,
        public ?string $column = null
    ) {}
}
