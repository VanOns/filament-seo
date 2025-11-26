<?php

namespace VanOns\FilamentSeo\AI\Prompts;

use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class GenerateDescription extends Prompt
{
    public function outputSchema(): ObjectSchema
    {
        return new ObjectSchema(
            name: 'seo_meta_description',
            description: 'A meta description for a webpage optimized for SEO',
            properties: [
                new StringSchema(name: 'output', description: 'The output that will be used for the meta description'),
            ],
            requiredFields: ['output'],
        );
    }

    public function systemPrompt(): string
    {
        return <<<EOT
        You are a world-class SEO expert.
        - Generate an optimized meta description based on the provided content as output.
        - The output MUST fit within 160 characters.
        - If a focus keyphrase is provided, it MUST be included.
        - Use active voice.
        EOT;
    }

    public function userPrompt(): string
    {
        $focusKeyphrase = $this->seoable->getFocusKeyphrase() ?? 'none';
        $title = $this->seoable->getSeoTitle()[0] ?? 'Untitled';
        $content = $this->seoable->getSeoContent();

        return <<< EOT
        Focus Keyphrase:
        {$focusKeyphrase}
        Title:
        {$title}
        Content:
        {$content}
        EOT;
    }
}
