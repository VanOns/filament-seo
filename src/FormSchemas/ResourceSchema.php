<?php

namespace VanOns\FilamentSeo\FormSchemas;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use VanOns\FilamentSeo\Contracts\ResourceSchema as ResourceSchemaContract;
use VanOns\FilamentSeo\Contracts\Seoable;
use VanOns\FilamentSeo\Fields\SeoDescriptionInput;
use VanOns\FilamentSeo\Fields\SeoTitleInput;
use VanOns\FilamentSeo\Schemas\Analysis;

class ResourceSchema implements ResourceSchemaContract
{
    public static string $seoPrefix = 'seo.';

    public function titleField(): Field
    {
        return SeoTitleInput::make(static::$seoPrefix . 'title')
            ->maxRecommendedLength(70)
            ->label(__('filament-seo::seo.page_title.label'));
    }

    public function descriptionField(): Field
    {
        return SeoDescriptionInput::make(static::$seoPrefix . 'description')
            ->maxRecommendedLength(160)
            ->label(__('filament-seo::seo.description.label'));
    }

    public function noIndexField(): Field
    {
        return Toggle::make(static::$seoPrefix . 'noindex')
            ->label(__('filament-seo::seo.no_index'))
            ->helperText(__('filament-seo::seo.no_index_help'));
    }

    public function noFollowField(): Field
    {
        return Toggle::make(static::$seoPrefix . 'nofollow')
            ->label(__('filament-seo::seo.no_follow'))
            ->helperText(__('filament-seo::seo.no_follow_help'));
    }

    public function canonicalField(): Field
    {
        return Select::make(static::$seoPrefix . 'canonical')
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
        return TextInput::make(static::$seoPrefix . 'canonical_url')
            ->label(__('filament-seo::seo.custom_url'))
            ->placeholder(__('https://example.com/page'))
            ->hidden(fn (Get $get) => $get(static::$seoPrefix . 'canonical') !== 'url');
    }

    public function ogTitleField(): Field
    {
        return SeoTitleInput::make(static::$seoPrefix . 'og_title')
            ->maxRecommendedLength(70)
            ->label(__('filament-seo::seo.social_title'));
    }

    public function ogDescriptionField(): Field
    {
        return SeoDescriptionInput::make(static::$seoPrefix . 'og_description')
            ->maxRecommendedLength(160)
            ->label(__('filament-seo::seo.social_description'))
            ->placeholder(function (?Model $record) {
                if (!$record instanceof Seoable) {
                    return null;
                }

                return $record->getSeoOgDescription();
            });
    }

    public function ogImageField(): Field
    {
        return FileUpload::make(static::$seoPrefix . 'og_image')
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
        return Select::make(static::$seoPrefix . 'change_frequency')
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
        return Select::make(static::$seoPrefix . 'priority')
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
        return TextInput::make(static::$seoPrefix . 'focus_keyphrase')
            ->label(__('filament-seo::seo.focus_keyphrase.label'))
            ->helperText(__('filament-seo::seo.focus_keyphrase.help'))
            ->live();
    }

    public function analysisSchema(?Model $record, Get $get, string $operation, Component $livewire): ?Analysis
    {
        if ($operation === 'create' && $livewire instanceof CreateRecord) {
            $record = new ($livewire->getModel());
        }

        if (!$record instanceof Seoable) {
            return null;
        }

        $clone = clone $record;

        /**
         * @var Model&Seoable $clone
         */
        return Analysis::make()
            ->analysers($clone->getSeoAnalysers())
            ->seoable($clone->fill($get()));
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
