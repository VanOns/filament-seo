<?php

namespace VanOns\FilamentSeo\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;
use VanOns\FilamentSeo\AI\Prompts\Prompt;
use VanOns\FilamentSeo\Contracts\Seoable;

class GenerateMetadata extends Action
{
    protected Seoable $seoable;

    protected Closure $onResponse;

    protected Prompt $prompt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-seo::seo.use_ai'))
            ->icon(Heroicon::Sparkles)
            ->action(function () {
                $response = $this->prompt->seoable($this->seoable)->generate();

                if (isset($this->onResponse)) {
                    ($this->onResponse)($response);
                }
            });
    }

    public function seoable(Seoable $seoable): static
    {
        $this->seoable = $seoable;

        return $this;
    }

    public function prompt(Prompt $prompt): static
    {
        $this->prompt = $prompt;

        return $this;
    }

    public function onResponse(Closure $callback): static
    {
        $this->onResponse = $callback;

        return $this;
    }
}
