<?php

use Tests\Fixtures\FakeSeoable;
use VanOns\FilamentSeo\Analysis\Analysers\ContentLength;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;

function words(int $count): string
{
    return implode(' ', array_fill(0, $count, 'word'));
}

it('returns null when content is absent', function () {
    $result = (new ContentLength())->analyse(new FakeSeoable());

    expect($result)->toBeNull();
});

it('fails when word count is below 200', function () {
    $result = (new ContentLength())->analyse(new FakeSeoable(content: words(150)));

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('warns when word count is between 200 and 299', function () {
    $result = (new ContentLength())->analyse(new FakeSeoable(content: words(250)));

    expect($result->status)->toBe(AnalysisStatus::WARNING);
});

it('warns at the lower boundary of 200 words', function () {
    $result = (new ContentLength())->analyse(new FakeSeoable(content: words(200)));

    expect($result->status)->toBe(AnalysisStatus::WARNING);
});

it('passes when word count meets the recommended minimum', function () {
    $result = (new ContentLength())->analyse(new FakeSeoable(content: words(300)));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('passes when word count exceeds the recommended minimum', function () {
    $result = (new ContentLength())->analyse(new FakeSeoable(content: words(500)));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});
