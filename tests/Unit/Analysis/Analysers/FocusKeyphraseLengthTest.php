<?php

use Tests\Fixtures\FakeSeoable;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseLength;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;

it('fails when no keyphrase is set', function () {
    $result = (new FocusKeyphraseLength())->analyse(new FakeSeoable());

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('passes when keyphrase has one word', function () {
    $result = (new FocusKeyphraseLength())->analyse(new FakeSeoable(focusKeyphrase: 'seo'));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('passes when keyphrase has exactly the max recommended words', function () {
    $result = (new FocusKeyphraseLength())->analyse(new FakeSeoable(focusKeyphrase: 'best seo tips guide'));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('warns when keyphrase slightly exceeds the recommended word count', function () {
    $result = (new FocusKeyphraseLength())->analyse(new FakeSeoable(focusKeyphrase: 'best seo tips for website'));

    expect($result->status)->toBe(AnalysisStatus::WARNING);
});

it('warns at the boundary of double the recommended word count', function () {
    $result = (new FocusKeyphraseLength())->analyse(new FakeSeoable(focusKeyphrase: 'one two three four five six seven eight'));

    expect($result->status)->toBe(AnalysisStatus::WARNING);
});

it('fails when keyphrase far exceeds the recommended word count', function () {
    $result = (new FocusKeyphraseLength())->analyse(new FakeSeoable(focusKeyphrase: 'one two three four five six seven eight nine'));

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('respects a custom word count', function () {
    $result = (new FocusKeyphraseLength(words: 2))->analyse(new FakeSeoable(focusKeyphrase: 'seo tips'));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});
