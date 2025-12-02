<?php

namespace VanOns\FilamentSeo\Schemas;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Prism\Prism\Structured\Response;
use VanOns\FilamentSeo\Actions\GenerateMetadata;
use VanOns\FilamentSeo\AI\Prompts\GenerateDescription;
use VanOns\FilamentSeo\AI\Prompts\GeneratePageTitle;
use VanOns\FilamentSeo\AI\Prompts\Prompt;
use VanOns\FilamentSeo\Contracts\Seoable;
use VanOns\FilamentSeo\Fields\SeoDescriptionInput;
use VanOns\FilamentSeo\Fields\SeoTitleInput;

class SeoFields implements \VanOns\FilamentSeo\Contracts\SeoFields
{
    public function __construct(
        public string $seoPrefix = 'seo.'
    ) {
    }

    public static function make(string $seoPrefix = 'seo.'): static
    {
        return app(static::class, ['seoPrefix' => $seoPrefix]);
    }

    public function titleField(): Field
    {
        return SeoTitleInput::make($this->seoPrefix . 'title')
            ->maxRecommendedLength(70)
            ->label(__('filament-seo::seo.page_title.label'))
            ->afterLabel(
                config('seo.ai.enabled', false)
                    ? $this->generateMetadataAction(new GeneratePageTitle())
                    : null
            );
    }

    public function descriptionField(): Field
    {
        return SeoDescriptionInput::make($this->seoPrefix . 'description')
            ->maxRecommendedLength(160)
            ->label(__('filament-seo::seo.description.label'))
            ->afterLabel(
                config('seo.ai.enabled', false)
                    ? $this->generateMetadataAction(new GenerateDescription())
                    : null
            );
    }

    public function noIndexField(): Field
    {
        return Toggle::make($this->seoPrefix . 'noindex')
            ->label(__('filament-seo::seo.no_index'))
            ->helperText(__('filament-seo::seo.no_index_help'));
    }

    public function noFollowField(): Field
    {
        return Toggle::make($this->seoPrefix . 'nofollow')
            ->label(__('filament-seo::seo.no_follow'))
            ->helperText(__('filament-seo::seo.no_follow_help'));
    }

    public function canonicalField(): Field
    {
        return Select::make($this->seoPrefix . 'canonical')
            ->label(__('filament-seo::seo.canonical_url'))
            ->helperText(__('filament-seo::seo.canonical_url_help'))
            ->options([
                null => __('filament-seo::seo.this_page'),
                'url' => __('filament-seo::seo.custom_url'),
            ])
            ->selectablePlaceholder(false)
            ->live();
    }

    public function canonicalUrlField(): Field
    {
        return TextInput::make($this->seoPrefix . 'canonical_url')
            ->label(__('filament-seo::seo.custom_url'))
            ->placeholder(__('https://example.com/page'))
            ->hidden(fn (Get $get) => $get($this->seoPrefix . 'canonical') !== 'url');
    }

    public function ogTitleField(): Field
    {
        return SeoTitleInput::make($this->seoPrefix . 'og_title')
            ->maxRecommendedLength(70)
            ->label(__('filament-seo::seo.social_title'))
            ->fallbackField($this->seoPrefix . 'title');
    }

    public function ogDescriptionField(): Field
    {
        return SeoDescriptionInput::make($this->seoPrefix . 'og_description')
            ->maxRecommendedLength(160)
            ->label(__('filament-seo::seo.social_description'))
            ->fallbackField($this->seoPrefix . 'description');
    }

    public function ogImageField(): Field
    {
        return FileUpload::make($this->seoPrefix . 'og_image')
            ->label(__('filament-seo::seo.social_image'))
            ->imageResizeMode('cover')
            ->imageEditor()
            ->imageCropAspectRatio('1200:630')
            ->imageResizeTargetWidth('1200')
            ->imageResizeTargetHeight('630')
            ->image()
            ->disk('public')
            ->directory('social-images');
    }

    public function changeFrequencyField(): Field
    {
        return Select::make($this->seoPrefix . 'change_frequency')
            ->label(__('filament-seo::seo.change_frequency'))
            ->helperText(__('filament-seo::seo.change_frequency_help'))
            ->options([
                'always' => __('filament-seo::seo.frequency.always'),
                'hourly' => __('filament-seo::seo.frequency.hourly'),
                'daily' => __('filament-seo::seo.frequency.daily'),
                'weekly' => __('filament-seo::seo.frequency.weekly'),
                'monthly' => __('filament-seo::seo.frequency.monthly'),
                'yearly' => __('filament-seo::seo.frequency.yearly'),
                'never' => __('filament-seo::seo.frequency.never'),
            ]);
    }

    public function priorityField(): Field
    {
        return Select::make($this->seoPrefix . 'priority')
            ->label(__('filament-seo::seo.priority'))
            ->helperText(__('filament-seo::seo.priority_help'))
            ->options([
                '0.1' => '0.1',
                '0.2' => '0.2',
                '0.3' => '0.3',
                '0.4' => '0.4',
                '0.5' => '0.5',
                '0.6' => '0.6',
                '0.7' => '0.7',
                '0.8' => '0.8',
                '0.9' => '0.9',
                '1.0' => '1.0',
            ]);
    }

    public function focusKeyphraseField(): Field
    {
        return TextInput::make($this->seoPrefix . 'focus_keyphrase')
            ->label(__('filament-seo::seo.focus_keyphrase.label'))
            ->helperText(__('filament-seo::seo.focus_keyphrase.help'))
            ->live();
    }

    /**
     * @param Model|null $record
     * @param Get $get
     * @param string $operation
     * @param Component $livewire
     * @return (Model&Seoable)|null
     */
    protected function getRecord(?Model $record, Get $get, string $operation, Component $livewire): ?Model
    {
        if ($operation === 'create' && $livewire instanceof CreateRecord) {
            $record = new ($livewire->getModel());
        }

        if (!$record instanceof Seoable) {
            return null;
        }

        /** @var Model&Seoable $record */
        $clone = clone $record;

        return $clone->fill($get());
    }

    public function analysisSchema(?Model $record, Get $get, string $operation, Component $livewire): array
    {
        $record = $this->getRecord($record, $get, $operation, $livewire);

        return Analysis::make()
            ->analysers($record->getSeoAnalysers())
            ->seoable($record)
            ->schema();
    }

    public function generateMetadataAction(Prompt $prompt): Closure
    {
        return function (Field $component, ?Model $record, Get $get, Set $set, string $operation, Component $livewire) use ($prompt) {
            $record = $this->getRecord($record, $get, $operation, $livewire);

            return GenerateMetadata::make('generate_metadata_' . $component->getName())
                ->seoable($record)
                ->prompt($prompt)
                ->onResponse(function (?Response $response) use ($set, $component) {
                    if ($output = $response->structured['output'] ?? null) {
                        $set($component->getName(), $output);
                    }

                    Notification::make()
                        ->title(__('filament-seo::seo.ai_generation_completed'))
                        ->success()
                        ->send();
                });
        };
    }

    public function schema(): array
    {
        return [
            Grid::make(3)->schema([
                Section::make()->schema([
                    Section::make(__('filament-seo::seo.basic'))->schema([
                        $this->focusKeyphraseField(),
                        $this->titleField(),
                        $this->descriptionField(),
                    ]),
                    Section::make(__('filament-seo::seo.advanced'))->schema([
                        Group::make([
                            $this->noIndexField(),
                            $this->noFollowField(),
                        ])->columns(2),
                        $this->canonicalField(),
                        $this->canonicalUrlField(),
                    ]),
                    Section::make(__('filament-seo::seo.social'))->description(__('filament-seo::seo.social_help_text'))->schema([
                        $this->ogTitleField(),
                        $this->ogDescriptionField(),
                        $this->ogImageField(),
                    ]),
                    Section::make(__('filament-seo::seo.sitemap'))->schema([
                        $this->changeFrequencyField(),
                        $this->priorityField(),
                    ]),
                ])
                    ->contained(false)
                    ->columnSpan(2),
                Section::make(__('filament-seo::seo.analysis'))->schema($this->analysisSchema(...)),
            ]),
        ];
    }
}
