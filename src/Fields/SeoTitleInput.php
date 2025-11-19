<?php

namespace VanOns\FilamentSeo\Fields;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use VanOns\FilamentSeo\Concerns\HasRecommendedLength;
use VanOns\FilamentSeo\Schemas\RecommendedLength;

class SeoTitleInput extends TextInput
{
    use HasRecommendedLength;
    protected ?string $fallbackField = 'title';

    public function setUp(): void
    {
        $suffix = ' - ' . config('app.name');

        $this->suffix($suffix)
            ->live()
            ->belowContent(function (?string $state, Get $get) {
                return RecommendedLength::make()
                    ->maxRecommendedLength($this->maxRecommendedLength)
                    ->subject($state ?: $get($this->fallbackField));
            })
            ->placeholder(function (Get $get) {
                return $get($this->fallbackField);
            });
    }

    public function fallbackField(?string $field): static
    {
        $this->fallbackField = $field;

        return $this;
    }
}
