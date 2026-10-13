<?php

namespace VanOns\FilamentSeo\Fields;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use VanOns\FilamentSeo\AI\Prompts\GenerateDescription;
use VanOns\FilamentSeo\Concerns\HasMetadataGeneration;
use VanOns\FilamentSeo\Concerns\HasRecommendedLength;
use VanOns\FilamentSeo\Schemas\RecommendedLength;

class SeoDescriptionInput extends Textarea
{
    use HasRecommendedLength;
    use HasMetadataGeneration;

    protected ?string $fallbackField = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->live()
            ->belowContent(function (?string $state, Get $get) {
                $subject = $state;

                if (!$subject && $this->fallbackField) {
                    $subject = $get($this->fallbackField);
                }

                return RecommendedLength::make()
                    ->maxRecommendedLength($this->maxRecommendedLength)
                    ->subject($subject)
                    ->schema();
            })
            ->placeholder(function (Get $get) {
                if (!$this->fallbackField) {
                    return null;
                }

                return $get($this->fallbackField);
            })
            ->extraAttributes(function (Field $component) {
                return $this->generateMetadataAlpineListener($component);
            })
            ->afterLabel($this->generateMetadataAction(new GenerateDescription()));
    }

    public function fallbackField(?string $field): static
    {
        $this->fallbackField = $field;

        return $this;
    }
}
