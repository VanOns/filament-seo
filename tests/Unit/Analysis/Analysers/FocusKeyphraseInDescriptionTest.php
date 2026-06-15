<?php

use Tests\Fixtures\FakeSeoable;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInDescription;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;

it('passes when description contains the exact keyphrase', function () {
    $result = (new FocusKeyphraseInDescription())->analyse(
        new FakeSeoable(description: 'Best seo tips for your website', focusKeyphrase: 'seo tips')
    );

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('passes when all keyphrase words are in the description but not as a phrase', function () {
    $result = (new FocusKeyphraseInDescription())->analyse(
        new FakeSeoable(description: 'SEO guide with tips inside', focusKeyphrase: 'seo tips')
    );

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('fails when keyphrase is not in the description', function () {
    $result = (new FocusKeyphraseInDescription())->analyse(
        new FakeSeoable(description: 'A guide for complete beginners', focusKeyphrase: 'seo tips')
    );

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('fails when no keyphrase is set', function () {
    $result = (new FocusKeyphraseInDescription())->analyse(
        new FakeSeoable(description: 'Best seo tips for your website')
    );

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('matches case-insensitively', function () {
    $result = (new FocusKeyphraseInDescription())->analyse(
        new FakeSeoable(description: 'Best SEO Tips for your website', focusKeyphrase: 'seo tips')
    );

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('allWordsPresent returns true when all words are present', function () {
    $analyser = new FocusKeyphraseInDescription();

    expect($analyser->allWordsPresent('seo guide with tips', 'seo tips'))->toBeTrue();
});

it('allWordsPresent returns false when a word is missing', function () {
    $analyser = new FocusKeyphraseInDescription();

    expect($analyser->allWordsPresent('a guide for beginners', 'seo tips'))->toBeFalse();
});
