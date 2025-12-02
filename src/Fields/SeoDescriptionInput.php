<?php

namespace VanOns\FilamentSeo\Fields;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use VanOns\FilamentSeo\Concerns\HasRecommendedLength;
use VanOns\FilamentSeo\Schemas\RecommendedLength;

class SeoDescriptionInput extends Textarea
{
    use HasRecommendedLength;

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
                    ->subject($subject);
            })
            ->placeholder(function (Get $get) {
                if (!$this->fallbackField) {
                    return null;
                }

                return $get($this->fallbackField);
            });
    }

    public function fallbackField(?string $field): static
    {
        $this->fallbackField = $field;

        return $this;
    }
}
