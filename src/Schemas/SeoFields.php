<?php

namespace VanOns\FilamentSeo\Schemas;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use VanOns\FilamentSeo\Concerns\GetsFilledRecord;
use VanOns\FilamentSeo\Fields\SeoDescriptionInput;
use VanOns\FilamentSeo\Fields\SeoTitleInput;

class SeoFields implements \VanOns\FilamentSeo\Contracts\SeoFields
{
    use GetsFilledRecord;

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
            ->label(__('filament-seo::seo.page_title.label'));
    }

    public function descriptionField(): Field
    {
        return SeoDescriptionInput::make($this->seoPrefix . 'description')
            ->maxRecommendedLength(160)
            ->label(__('filament-seo::seo.description.label'));
    }

    public function noIndexField(): Field
    {
        return Toggle::make($this->seoPrefix . 'noindex')
            ->label(__('filament-seo::seo.no_index'))
            ->helperText(__('filament-seo::seo.no_index_help'))
            ->live();
    }

    public function noFollowField(): Field
    {
        return Toggle::make($this->seoPrefix . 'nofollow')
            ->label(__('filament-seo::seo.no_follow'))
            ->helperText(__('filament-seo::seo.no_follow_help'))
            ->live();
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
            ->hidden(fn (Get $get) => $get($this->seoPrefix . 'canonical') !== 'url')
            ->live(onBlur: true);
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

    public function excludeFromSitemapField(): Field
    {
        return Toggle::make($this->seoPrefix . 'exclude_from_sitemap')
            ->label(__('filament-seo::seo.exclude_from_sitemap'))
            ->helperText(__('filament-seo::seo.exclude_from_sitemap_help'))
            ->live();
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
            ])
            ->hidden(fn (Get $get) => (bool) $get($this->seoPrefix . 'exclude_from_sitemap'));
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
            ])
            ->hidden(fn (Get $get) => (bool) $get($this->seoPrefix . 'exclude_from_sitemap'));
    }

    public function focusKeyphraseField(): Field
    {
        return TextInput::make($this->seoPrefix . 'focus_keyphrase')
            ->label(__('filament-seo::seo.focus_keyphrase.label'))
            ->helperText(__('filament-seo::seo.focus_keyphrase.help'))
            ->live();
    }

    public function analysisSchema(?Model $record, Get $get, string $operation, Component $livewire): array
    {
        $record = $this->getFilledRecord($record, $get, $operation, $livewire);

        return Analysis::make()
            ->analysers($record->getSeoAnalysers())
            ->seoable($record)
            ->schema();
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
                        $this->excludeFromSitemapField(),
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
