<?php

use Tests\Fixtures\FakeSeoable;
use VanOns\FilamentSeo\Analysis\Analysers\DescriptionLength;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;

it('fails when description is missing', function () {
    $result = (new DescriptionLength())->analyse(new FakeSeoable());

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('passes when description is within the limit', function () {
    $result = (new DescriptionLength())->analyse(new FakeSeoable(description: str_repeat('a', 120)));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('passes when description is exactly at max length', function () {
    $result = (new DescriptionLength())->analyse(new FakeSeoable(description: str_repeat('a', 160)));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('warns when description slightly exceeds the limit', function () {
    $result = (new DescriptionLength())->analyse(new FakeSeoable(description: str_repeat('a', 170)));

    expect($result->status)->toBe(AnalysisStatus::WARNING);
});

it('warns at the boundary of 120 percent', function () {
    $result = (new DescriptionLength())->analyse(new FakeSeoable(description: str_repeat('a', 192)));

    expect($result->status)->toBe(AnalysisStatus::WARNING);
});

it('fails when description exceeds 120 percent of max length', function () {
    $result = (new DescriptionLength())->analyse(new FakeSeoable(description: str_repeat('a', 193)));

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('respects a custom max length', function () {
    $result = (new DescriptionLength(maxRecommendedLength: 50))->analyse(new FakeSeoable(description: str_repeat('a', 50)));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});
