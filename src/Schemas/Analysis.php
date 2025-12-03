<?php

namespace VanOns\FilamentSeo\Schemas;

use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Contracts\Schema;
use VanOns\FilamentSeo\Contracts\Seoable;

class Analysis implements Schema
{
    /**
     * @var Collection<array-key, AnalyserInterface>
     */
    protected Collection $analysers;

    protected Seoable $seoable;

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @param array<AnalyserInterface|string> $analysers
     * @return $this
     */
    public function analysers(array $analysers): static
    {
        $this->analysers = collect($analysers)->map(function (AnalyserInterface|string $analyser) {
            if ($analyser instanceof AnalyserInterface) {
                return $analyser;
            }

            $instance = new $analyser();

            if (!$instance instanceof AnalyserInterface) {
                throw new InvalidArgumentException('Analyser must implement AnalyserInterface');
            }

            return $instance;
        });

        return $this;
    }

    /**
     * @param (Model&Seoable)|null $seoable
     * @return $this
     */
    public function seoable(?Model $seoable): static
    {
        if (!$seoable instanceof Seoable) {
            throw new InvalidArgumentException('Model must implement Seoable interface');
        }

        $this->seoable = $seoable;

        return $this;
    }

    public function schema(): array
    {
        return $this->analysers->map(
            fn (AnalyserInterface $analyser) => $analyser->analyse($this->seoable)
        )->filter()->map(
            fn (?AnalysisResult $result) => Flex::make([
                Icon::make($result->status->icon())->color($result->status->color())->grow(false),
                Text::make($result->message),
            ])
        )->toArray();
    }
}
