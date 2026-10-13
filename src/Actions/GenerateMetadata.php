<?php

namespace VanOns\FilamentSeo\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
                $key = 'filament-seo-ai:' . (Auth::id() ?? request()->ip());

                if (RateLimiter::tooManyAttempts($key, (int) config('seo.ai.rate_limit', 10))) {
                    Notification::make()
                        ->title(__('filament-seo::seo.ai_rate_limited', ['seconds' => RateLimiter::availableIn($key)]))
                        ->danger()
                        ->send();

                    return;
                }

                RateLimiter::hit($key);

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
