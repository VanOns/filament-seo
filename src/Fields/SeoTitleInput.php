<?php

namespace VanOns\FilamentSeo\Fields;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use VanOns\FilamentSeo\AI\Prompts\GeneratePageTitle;
use VanOns\FilamentSeo\Concerns\HasMetadataGeneration;
use VanOns\FilamentSeo\Concerns\HasRecommendedLength;
use VanOns\FilamentSeo\Contracts\SeoSettings;
use VanOns\FilamentSeo\Schemas\RecommendedLength;

class SeoTitleInput extends TextInput
{
    use HasRecommendedLength;
    use HasMetadataGeneration;

    protected ?string $fallbackField = null;

    public function setUp(): void
    {
        parent::setUp();

        $suffix = app(SeoSettings::class)->getSuffix();

        $this->suffix($suffix)
            ->live()
            ->belowContent(function (?string $state, Get $get) use ($suffix) {
                return RecommendedLength::make()
                    ->maxRecommendedLength($this->maxRecommendedLength)
                    ->subject($state ?: $get($this->getFallbackField()))
                    ->suffix($suffix)
                    ->schema();
            })
            ->placeholder(function (Get $get) {
                return $get($this->getFallbackField());
            })
            ->extraAttributes(function (Field $component) {
                return $this->generateMetadataAlpineListener($component);
            })
            ->afterLabel($this->generateMetadataAction(new GeneratePageTitle()));
    }

    public function getFallbackField(): string
    {
        if ($this->fallbackField) {
            return $this->fallbackField;
        }

        $model = $this->getModel();

        if ($model && method_exists($model, 'getDefaultSeoTitleField')) {
            return $model::getDefaultSeoTitleField();
        }

        return 'title';
    }

    public function fallbackField(?string $field): static
    {
        $this->fallbackField = $field;

        return $this;
    }
}
