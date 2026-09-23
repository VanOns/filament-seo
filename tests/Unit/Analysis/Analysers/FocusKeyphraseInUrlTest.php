<?php

use Tests\Fixtures\FakeSeoable;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInUrl;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;

it('returns null when url is absent', function () {
    $result = (new FocusKeyphraseInUrl())->analyse(
        new FakeSeoable(focusKeyphrase: 'seo tips')
    );

    expect($result)->toBeNull();
});

it('passes when url contains the exact keyphrase slug', function () {
    $result = (new FocusKeyphraseInUrl())->analyse(
        new FakeSeoable(focusKeyphrase: 'seo tips', urlValue: 'https://example.com/seo-tips')
    );

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('passes when url contains all keyphrase words separately', function () {
    $result = (new FocusKeyphraseInUrl())->analyse(
        new FakeSeoable(focusKeyphrase: 'seo tips', urlValue: 'https://example.com/seo-guide-tips')
    );

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('fails when url does not contain the keyphrase', function () {
    $result = (new FocusKeyphraseInUrl())->analyse(
        new FakeSeoable(focusKeyphrase: 'seo tips', urlValue: 'https://example.com/contact')
    );

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('fails when no keyphrase is set', function () {
    $result = (new FocusKeyphraseInUrl())->analyse(
        new FakeSeoable(urlValue: 'https://example.com/seo-tips')
    );

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('slugifies the keyphrase before checking', function () {
    $result = (new FocusKeyphraseInUrl())->analyse(
        new FakeSeoable(focusKeyphrase: 'SEO Tips', urlValue: 'https://example.com/seo-tips')
    );

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('allWordsPresent checks each slug word individually', function () {
    $analyser = new FocusKeyphraseInUrl();

    expect($analyser->allWordsPresent('https://example.com/seo-guide-tips', 'seo-tips'))->toBeTrue()
        ->and($analyser->allWordsPresent('https://example.com/contact', 'seo-tips'))->toBeFalse();
});
