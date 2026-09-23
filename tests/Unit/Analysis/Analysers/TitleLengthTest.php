<?php

use Tests\Fixtures\FakeSeoable;
use VanOns\FilamentSeo\Analysis\Analysers\TitleLength;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;

it('fails when title is missing', function () {
    $result = (new TitleLength())->analyse(new FakeSeoable());

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('passes when title is within the limit', function () {
    $result = (new TitleLength())->analyse(new FakeSeoable(title: str_repeat('a', 50)));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('passes when title is exactly at max length', function () {
    $result = (new TitleLength())->analyse(new FakeSeoable(title: str_repeat('a', 70)));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('warns when title slightly exceeds the limit', function () {
    $result = (new TitleLength())->analyse(new FakeSeoable(title: str_repeat('a', 75)));

    expect($result->status)->toBe(AnalysisStatus::WARNING);
});

it('warns at the boundary of 120 percent', function () {
    $result = (new TitleLength())->analyse(new FakeSeoable(title: str_repeat('a', 84)));

    expect($result->status)->toBe(AnalysisStatus::WARNING);
});

it('fails when title exceeds 120 percent of max length', function () {
    $result = (new TitleLength())->analyse(new FakeSeoable(title: str_repeat('a', 85)));

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('respects a custom max length', function () {
    $result = (new TitleLength(maxRecommendedLength: 10))->analyse(new FakeSeoable(title: str_repeat('a', 10)));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});
