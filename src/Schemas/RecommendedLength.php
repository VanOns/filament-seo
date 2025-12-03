<?php

namespace VanOns\FilamentSeo\Schemas;

use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Text;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use VanOns\FilamentSeo\Contracts\Schema;

class RecommendedLength implements Schema
{
    public ?int $maxRecommendedLength = null;

    public int $maxSuccessPercentage = 100;

    public int $maxWarningPercentage = 120;

    protected ?string $subject = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function maxRecommendedLength(int $maxRecommendedLength): static
    {
        $this->maxRecommendedLength = $maxRecommendedLength;

        return $this;
    }

    public function subject(?string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    protected function percentage(): float
    {
        $length = strlen($this->subject);

        if (!$this->maxRecommendedLength) {
            return 0;
        }

        return max($length / $this->maxRecommendedLength * 100, 0);

    }

    protected function color(): array
    {
        $percentage = $this->percentage();
        return match(true) {
            $percentage > 0 && $percentage <= 100 => Color::Green,
            $percentage > 100 && $percentage <= 120 => Color::Yellow,
            default => Color::Red,
        };
    }

    protected function icon(): Icon
    {
        $percentage = $this->percentage();
        $icon = match(true) {
            $percentage > 0 && $percentage <= 100 => Heroicon::CheckCircle,
            $percentage > 100 && $percentage <= 120 => Heroicon::ExclamationCircle,
            default => Heroicon::XCircle,
        };

        return Icon::make($icon)->color($this->color());
    }

    protected function text()
    {
        $length = strlen($this->subject);
        return Text::make(__('filament-seo::seo.count_characters', ['count' => "{$length} / {$this->maxRecommendedLength}"]));
    }

    public function schema(): array
    {
        return [
            Flex::make([
                $this->icon()->grow(false),
                $this->text()->grow(),
            ])->verticallyAlignStart(),
        ];
    }
}
