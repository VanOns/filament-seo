@php
    /**
     * @var \Filament\Schemas\Components\View $schemaComponent
     */

    /** @var \VanOns\FilamentSeo\Fields\SeoTitleInput $field */
    $field = $schemaComponent->getContainer()->getParentComponent();
    $length = Str::length($schemaComponent->getState());
    $maxRecommendedLength = $field->getMaxRecommendedLength();
    $percentage = $length / $maxRecommendedLength * 100;
@endphp

<div class="flex-grow p-2 bg-gray-500">
    {{ $percentage }}
</div>
