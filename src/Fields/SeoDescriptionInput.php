<?php

namespace VanOns\FilamentSeo\Fields;

use Filament\Forms\Components\Textarea;
use VanOns\FilamentSeo\Concerns\HasRecommendedLength;
use VanOns\FilamentSeo\Schemas\RecommendedLength;

class SeoDescriptionInput extends Textarea
{
    use HasRecommendedLength;

    public function setUp(): void
    {
        $this->live()
            ->belowContent(function (?string $state) {
                return RecommendedLength::make()
                    ->maxRecommendedLength($this->maxRecommendedLength)
                    ->subject($state);
            });
    }
}
