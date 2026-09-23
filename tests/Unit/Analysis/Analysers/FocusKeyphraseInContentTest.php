<?php

use Tests\Fixtures\FakeSeoable;
use VanOns\FilamentSeo\Analysis\Analysers\FocusKeyphraseInContent;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;

it('returns null when content is absent', function () {
    $result = (new FocusKeyphraseInContent())->analyse(new FakeSeoable(focusKeyphrase: 'seo'));

    expect($result)->toBeNull();
});

it('fails when no keyphrase is set', function () {
    $content = 'seo is important for every website today here';
    $result = (new FocusKeyphraseInContent())->analyse(new FakeSeoable(content: $content));

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('fails when keyphrase is not present in content', function () {
    $content = 'this is some content without the search term present';
    $result = (new FocusKeyphraseInContent())->analyse(new FakeSeoable(content: $content, focusKeyphrase: 'seo'));

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('passes when keyphrase appears the correct number of times', function () {
    // 9 words → max=1 (≤50), count=1 → passed
    $content = 'seo is very important for your website today okay';
    $result = (new FocusKeyphraseInContent())->analyse(new FakeSeoable(content: $content, focusKeyphrase: 'seo'));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('fails when keyphrase appears too many times in short content', function () {
    // 9 words → max=1 (≤50), "seo" appears twice → count=2 > max → failed
    $content = 'seo and seo are important for your website today';
    $result = (new FocusKeyphraseInContent())->analyse(new FakeSeoable(content: $content, focusKeyphrase: 'seo'));

    expect($result->status)->toBe(AnalysisStatus::FAILED);
});

it('handles multi-word keyphrases using the minimum word occurrence', function () {
    // "seo tips" → counts min(occurrences of "seo", occurrences of "tips")
    // "seo" appears once, "tips" appears once → count=1 → passed
    $content = 'these seo tips will help you improve your website';
    $result = (new FocusKeyphraseInContent())->analyse(new FakeSeoable(content: $content, focusKeyphrase: 'seo tips'));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('counts keyphrase presence case-insensitively', function () {
    $content = 'SEO is very important for your website today okay';
    $result = (new FocusKeyphraseInContent())->analyse(new FakeSeoable(content: $content, focusKeyphrase: 'seo'));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('counts presence across multiple paragraphs', function () {
    // Each paragraph has the keyphrase once, total = 2
    // 100 words total → min=1, max=round(0.03*100)=3 → count=2 → passed
    $paragraph = implode(' ', array_fill(0, 49, 'word'));
    $content = 'seo ' . $paragraph . "\nseo " . $paragraph;
    $result = (new FocusKeyphraseInContent())->analyse(new FakeSeoable(content: $content, focusKeyphrase: 'seo'));

    expect($result->status)->toBe(AnalysisStatus::PASSED);
});

it('countPresence returns minimum word occurrence across keyphrase words', function () {
    $analyser = new FocusKeyphraseInContent();

    // "seo" appears 2 times, "tips" appears 1 time → min = 1
    expect($analyser->countPresence('seo seo tips today', 'seo tips'))->toBe(1);
});

it('countPresence returns zero when keyphrase word is absent', function () {
    $analyser = new FocusKeyphraseInContent();

    expect($analyser->countPresence('this is some text', 'seo'))->toBe(0);
});
