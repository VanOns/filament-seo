<?php

namespace VanOns\FilamentSeo\FormSchemas;

use Closure;
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
use VanOns\FilamentSeo\Analysis\Analysers\DescriptionLength;
use VanOns\FilamentSeo\Analysis\Analysers\TitleLength;
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
            ->label(__('Page title'));
    }

    public function descriptionField(): Field
    {
        return SeoDescriptionInput::make(static::$seoPrefix . 'description')
            ->maxRecommendedLength(160)
            ->label(__('Description'));
    }

    public function noIndexField(): Field
    {
        return Toggle::make(static::$seoPrefix . 'noindex')
            ->label(__('No index'))
            ->helperText(__('Prevent search engines from indexing this page'));
    }

    public function noFollowField(): Field
    {
        return Toggle::make(static::$seoPrefix . 'nofollow')
            ->label(__('No follow'))
            ->helperText(__('Prevent search engines from following links on this page'));
    }

    public function canonicalField(): Field
    {
        return Select::make(static::$seoPrefix . 'canonical')
            ->label(__('Canonical URL'))
            ->helperText(__('The preferred version of a set of pages with highly similar content'))
            ->options([
                null => __('This page'),
                'url' => __('Custom URL'),
            ])
            ->selectablePlaceholder(false)
            ->live();
    }

    public function canonicalUrlField(): Field
    {
        return TextInput::make(static::$seoPrefix . 'canonical_url')
            ->label(__('Custom URL'))
            ->placeholder(__('https://example.com/page'))
            ->hidden(fn (Get $get) => $get(static::$seoPrefix . 'canonical') !== 'url');
    }

    public function ogTitleField(): Field
    {
        return SeoTitleInput::make(static::$seoPrefix . 'og_title')
            ->maxRecommendedLength(70)
            ->label(__('Social Title'));
    }

    public function ogDescriptionField(): Field
    {
        return SeoDescriptionInput::make(static::$seoPrefix . 'og_description')
            ->maxRecommendedLength(160)
            ->label(__('Social Description'))
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
            ->label(__('Social Image'))
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
            ->label(__('Change frequency'))
            ->helperText(__('How frequently the page is likely to change'))
            ->options([
                'always' => __('Always'),
                'hourly' => __('Hourly'),
                'daily' => __('Daily'),
                'weekly' => __('Weekly'),
                'monthly' => __('Monthly'),
                'yearly' => __('Yearly'),
                'never' => __('Never'),
            ]);
    }

    public function priorityField(): Field
    {
        return Select::make(static::$seoPrefix . 'priority')
            ->label(__('Priority'))
            ->helperText(__('The priority of this URL relative to other URLs on your site'))
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
            ->label(__('Focus Keyphrase'))
            ->helperText(__('The main keyphrase you want this page to rank for'));
    }

    public function analysisSchema(?Model $record, Get $get, string $operation, Component $livewire): ?Analysis
    {
        if ($operation === 'create' && $livewire instanceof CreateRecord) {
            $record = new ($livewire->getModel());
        }

        if (!$record instanceof Seoable) {
            return null;
        }

        /**
         * @var Model&Seoable $record
         */
        return Analysis::make()->analysers([
            TitleLength::class,
            DescriptionLength::class,
        ])->seoable(
            $record->replicate()->fill($get())
        );
    }

    public function schema(): array
    {
        return [
            Grid::make(3)->schema([
                Section::make()->schema([
                    Section::make(__('Basic'))->schema([
                        $this->focusKeyphraseField(),
                        $this->titleField(),
                        $this->descriptionField(),
                    ]),
                    Section::make(__('Advanced'))->schema([
                        Group::make([
                            $this->noIndexField(),
                            $this->noFollowField(),
                        ])->columns(2),
                        $this->canonicalField(),
                        $this->canonicalUrlField(),
                    ]),
                    Section::make(__('Social'))->description(__('Open Graph meta data for sharing on social platforms.'))->schema([
                        $this->ogTitleField(),
                        $this->ogDescriptionField(),
                        $this->ogImageField(),
                    ]),
                    Section::make(__('Sitemap'))->schema([
                        $this->changeFrequencyField(),
                        $this->priorityField(),
                    ]),
                ])
                    ->contained(false)
                    ->columnSpan(2),
                Section::make(__('Analysis'))->schema($this->analysisSchema(...)),
            ]),
        ];
    }
}
