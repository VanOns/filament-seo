<?php

namespace VanOns\FilamentSeo\Fields;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use VanOns\FilamentSeo\AI\Prompts\GeneratePageTitle;
use VanOns\FilamentSeo\Concerns\HasMetadataGeneration;
use VanOns\FilamentSeo\Concerns\HasRecommendedLength;
use VanOns\FilamentSeo\Schemas\RecommendedLength;

class SeoTitleInput extends TextInput
{
    use HasRecommendedLength;
    use HasMetadataGeneration;
    protected ?string $fallbackField = 'title';

    public function setUp(): void
    {
        $suffix = ' - ' . config('app.name');

        $this->suffix($suffix)
            ->live()
            ->belowContent(function (?string $state, Get $get) {
                return RecommendedLength::make()
                    ->maxRecommendedLength($this->maxRecommendedLength)
                    ->subject($state ?: $get($this->fallbackField))
                    ->schema();
            })
            ->placeholder(function (Get $get) {
                return $get($this->fallbackField);
            })
            ->extraAttributes(function (Field $component) {
                return $this->generateMetadataAlpineListener($component);
            })
            ->afterLabel($this->generateMetadataAction(new GeneratePageTitle()));
    }

    public function fallbackField(?string $field): static
    {
        $this->fallbackField = $field;

        return $this;
    }
}
