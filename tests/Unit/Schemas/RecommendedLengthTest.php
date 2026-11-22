<?php

use VanOns\FilamentSeo\Schemas\RecommendedLength;

function recommendedLength(string $subject, ?string $suffix, int $maxRecommendedLength): int
{
    $instance = RecommendedLength::make()
        ->maxRecommendedLength($maxRecommendedLength)
        ->subject($subject)
        ->suffix($suffix);

    return (new ReflectionMethod($instance, 'length'))->invoke($instance);
}

it('counts only the subject when there is no suffix', function () {
    expect(recommendedLength(str_repeat('a', 65), null, 70))->toBe(65);
});

it('adds the suffix length to the count', function () {
    expect(recommendedLength(str_repeat('a', 65), ' - My Site', 70))->toBe(75);
});

it('flags a title as over the limit once the suffix is included', function () {
    $instance = RecommendedLength::make()
        ->maxRecommendedLength(70)
        ->subject(str_repeat('a', 65))
        ->suffix(' - My Site');

    $percentage = (new ReflectionMethod($instance, 'percentage'))->invoke($instance);

    expect($percentage)->toBeGreaterThan(100);
});
