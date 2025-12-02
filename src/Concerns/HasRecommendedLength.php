<?php

namespace VanOns\FilamentSeo\Concerns;

use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

trait HasRecommendedLength
{
    protected ?int $maxRecommendedLength = null;

    public function maxRecommendedLength(int $length): static
    {
        $this->maxRecommendedLength = $length;

        return $this;
    }

    public function getRecommendedLengthSchema(?string $state): ?Schema
    {
        if (!$this->maxRecommendedLength) {
            return null;
        }

        $length = strlen($state);
        $percentage = max($length / $this->maxRecommendedLength * 100, 0);
        $color = match(true) {
            $percentage > 0 && $percentage <= 100 => Color::Green,
            $percentage > 100 && $percentage <= 120 => Color::Yellow,
            default => Color::Red,
        };

        $icon = match(true) {
            $percentage > 0 && $percentage <= 100 => Heroicon::CheckCircle,
            $percentage > 100 && $percentage <= 120 => Heroicon::ExclamationCircle,
            default => Heroicon::XCircle,
        };

        return Schema::start([
            Icon::make($icon)->color($color),
            Text::make("{$length} / {$this->maxRecommendedLength} tekens"),
        ]);
    }
}
