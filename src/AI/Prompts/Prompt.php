<?php

namespace VanOns\FilamentSeo\AI\Prompts;

use Prism\Prism\Facades\Prism;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Structured\Response;
use VanOns\FilamentSeo\Contracts\Seoable;

abstract class Prompt
{
    protected Seoable $seoable;

    abstract public function outputSchema(): ObjectSchema;
    abstract public function systemPrompt(): string;
    abstract public function userPrompt(): string;

    public function seoable(Seoable $seoable): static
    {
        $this->seoable = $seoable;

        return $this;
    }

    public function generate(): Response
    {
        return Prism::structured()
            ->using(config('seo.ai.provider'), config('seo.ai.model'))
            ->withSchema($this->outputSchema())
            ->withSystemPrompt($this->systemPrompt())
            ->withPrompt($this->userPrompt())
            ->asStructured();
    }
}
