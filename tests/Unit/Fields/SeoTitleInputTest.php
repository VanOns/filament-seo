<?php

use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use VanOns\FilamentSeo\Fields\SeoTitleInput;

beforeEach(function () {
    config(['seo.settings.config.enable_suffix' => false]);
});

class TitleFieldModel
{
    public static function getDefaultSeoTitleField(): string
    {
        return 'name';
    }
}

it('falls back to the title field when there is no model', function () {
    $field = SeoTitleInput::make('seo.title');
    $field->container(Schema::make(Mockery::mock(HasSchemas::class)));

    expect($field->getFallbackField())->toBe('title');
});

it('uses the default seo title field of the model', function () {
    $field = SeoTitleInput::make('seo.title')->model(TitleFieldModel::class);

    expect($field->getFallbackField())->toBe('name');
});

it('prefers an explicitly configured fallback field', function () {
    $field = SeoTitleInput::make('seo.og_title')
        ->model(TitleFieldModel::class)
        ->fallbackField('seo.title');

    expect($field->getFallbackField())->toBe('seo.title');
});
