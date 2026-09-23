<?php

use Tests\Fixtures\FakeSeoable;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInTitle;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;

it('passes when title contains the exact keyphrase', function () {
    $result = (new FocusKeyphraseInTitle())->analyse(
        new FakeSeoable(title: 'Best SEO tips for beginners', focusKeyphrase: 'seo tips')
    );

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('warns when all keyphrase words are in the title but not as a phrase', function () {
    $result = (new FocusKeyphraseInTitle())->analyse(
        new FakeSeoable(title: 'SEO guide with tips for beginners', focusKeyphrase: 'seo tips')
    );

    expect($result->status)->toBe(AnalysisStatus::WARNING);
});

it('fails when keyphrase is not in the title at all', function () {
    $result = (new FocusKeyphraseInTitle())->analyse(
        new FakeSeoable(title: 'A guide for beginners', focusKeyphrase: 'seo tips')
    );

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('fails when no keyphrase is set', function () {
    $result = (new FocusKeyphraseInTitle())->analyse(
        new FakeSeoable(title: 'Best SEO tips')
    );

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('matches case-insensitively', function () {
    $result = (new FocusKeyphraseInTitle())->analyse(
        new FakeSeoable(title: 'Best SEO Tips for beginners', focusKeyphrase: 'seo tips')
    );

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('allWordsPresent returns true when all words are in the title', function () {
    $analyser = new FocusKeyphraseInTitle();

    expect($analyser->allWordsPresent('seo guide with tips', 'seo tips'))->toBeTrue();
});

it('allWordsPresent returns false when a word is missing', function () {
    $analyser = new FocusKeyphraseInTitle();

    expect($analyser->allWordsPresent('a guide for beginners', 'seo tips'))->toBeFalse();
});
