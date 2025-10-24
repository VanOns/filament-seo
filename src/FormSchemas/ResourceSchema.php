<?php

namespace VanOns\FilamentSeo\FormSchemas;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use VanOns\FilamentSeo\Contracts\ResourceSchema as ResourceSchemaContract;
use VanOns\FilamentSeo\Contracts\Seoable;

class ResourceSchema implements ResourceSchemaContract
{
    public static string $seoPrefix = 'seo.';

    public function titleField(): Field
    {
        $titleSeparater = '-';
        $title = config('app.name');

        return TextInput::make(static::$seoPrefix . 'title')
            ->label(__('Page title'))
            ->helperText(__('A title for the page, max 70 characters including the site name'))
            ->suffix($titleSeparater . ' ' . $title)
            ->placeholder(function (?Model $record) {
                if (!$record instanceof Seoable) {
                    return null;
                }

                return $record->getSeoTitle()[0] ?? null;
            });
    }

    public function descriptionField(): Field
    {
        return Textarea::make(static::$seoPrefix . 'description')
            ->label(__('Description'))
            ->helperText(__('A short description of the content, max 160 characters'))
            ->placeholder(function (?Model $record) {
                if (!$record instanceof Seoable) {
                    return null;
                }

                return $record->getSeoDescription();
            });
    }

    public function keywordsField(): Field
    {
        return Textarea::make(static::$seoPrefix . 'keywords')
            ->label(__('Keywords'))
            ->helperText(__('The keywords for the page, comma separated'));
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
        return TextInput::make(static::$seoPrefix . 'og_title')
            ->label(__('Social Title'))
            ->helperText(__('A title for the page, max 70 characters'))
            ->placeholder(function (?Model $record) {
                if (!$record instanceof Seoable) {
                    return null;
                }

                return $record->getSeoOgTitle()[0] ?? null;
            });
    }

    public function ogDescriptionField(): Field
    {
        return Textarea::make(static::$seoPrefix . 'og_description')
            ->label(__('Social Description'))
            ->helperText(__('A short description of the content, max 160 characters'))
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

    public function schema(): array
    {
        return [
            Section::make(__('Basic'))->schema([
                $this->titleField(),
                $this->descriptionField(),
                $this->keywordsField(),
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
        ];
    }
}
