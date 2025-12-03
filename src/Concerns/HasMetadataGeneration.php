<?php

namespace VanOns\FilamentSeo\Concerns;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Prism\Prism\Structured\Response;
use VanOns\FilamentSeo\Actions\GenerateMetadata;
use VanOns\FilamentSeo\AI\Prompts\Prompt;

trait HasMetadataGeneration
{
    use GetsFilledRecord;

    public function generateMetadataAction(Prompt $prompt): ?Closure
    {
        if (!config('seo.ai.enabled', false)) {
            return null;
        }

        return function (Field $component, ?Model $record, Get $get, string $operation, Component $livewire) use ($prompt) {
            $record = $this->getFilledRecord($record, $get, $operation, $livewire);

            return GenerateMetadata::make('generate_metadata_' . $component->getName())
                ->seoable($record)
                ->prompt($prompt)
                ->onResponse(function (?Response $response) use ($component, $livewire) {
                    if ($output = $response->structured['output'] ?? null) {
                        $livewire->dispatch($this->generateMetadataEventName($component), [
                            'value' => $output,
                        ]);
                    }

                    Notification::make()
                        ->title(__('filament-seo::seo.ai_generation_completed'))
                        ->success()
                        ->send();
                });
        };
    }

    public function generateMetadataAlpineListener(Field $field): array
    {
        $event = $this->generateMetadataEventName($field);
        return [
            "x-on:{$event}.window" => "\$wire.set('{$field->getStatePath()}', \$event.detail[0].value)",
        ];
    }

    public function generateMetadataEventName(Field $field): string
    {
        return 'metadata-generated-' . md5($field->getStatePath());
    }
}
