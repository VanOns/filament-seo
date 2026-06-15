<?php

use VanOns\FilamentSeo\Analysis\AnalyserInterface;
use VanOns\FilamentSeo\Analysis\AnalyserRegistry;
use VanOns\FilamentSeo\Analysis\Analysers\DescriptionLength;
use VanOns\FilamentSeo\Analysis\Analysers\TitleLength;
use VanOns\FilamentSeo\Analysis\AnalysisResult;
use VanOns\FilamentSeo\Analysis\AnalysisStatus;
use VanOns\FilamentSeo\Contracts\Seoable;

it('loads default analysers from config on construction', function () {
    $registry = new AnalyserRegistry();
    expect(count($registry->getAnalysers()))->toBeGreaterThan(0);
});

it('registers additional analysers by class name', function () {
    $registry = new AnalyserRegistry();
    $before = count($registry->getAnalysers());

    $registry->register([TitleLength::class]);

    expect(count($registry->getAnalysers()))->toBe($before + 1);
});

it('registers analyser instances directly', function () {
    $registry = new AnalyserRegistry();
    $before = count($registry->getAnalysers());

    $registry->register([new DescriptionLength()]);

    expect(count($registry->getAnalysers()))->toBe($before + 1);
});

it('unregisters analysers matching the callback', function () {
    $registry = new AnalyserRegistry();
    $registry->register([new TitleLength()]);
    $before = count($registry->getAnalysers());

    $registry->unregister(fn (AnalyserInterface $a) => $a instanceof TitleLength);

    expect(count($registry->getAnalysers()))->toBeLessThan($before)
        ->and(collect($registry->getAnalysers())->filter(fn ($a) => $a instanceof TitleLength))->toBeEmpty();
});

it('applies a filter callback to getAnalysers', function () {
    $registry = new AnalyserRegistry();
    $registry->filter(fn (array $analysers, $seoable) => array_filter(
        $analysers,
        fn ($a) => $a instanceof TitleLength
    ));

    $result = $registry->getAnalysers();

    expect($result)->not->toBeEmpty()
        ->and(collect($result)->every(fn ($a) => $a instanceof TitleLength))->toBeTrue();
});

it('returns all analysers when no filter is set', function () {
    $registry = new AnalyserRegistry();
    $registry->register([new TitleLength(), new DescriptionLength()]);

    expect(count($registry->getAnalysers()))->toBeGreaterThanOrEqual(2);
});

it('accepts a custom analyser implementation', function () {
    $custom = new class () implements AnalyserInterface {
        public function analyse(Seoable $record): ?AnalysisResult
        {
            return new AnalysisResult(AnalysisStatus::INFO, 'custom');
        }
    };

    $registry = new AnalyserRegistry();
    $registry->register([$custom]);

    expect(in_array($custom, $registry->getAnalysers(), true))->toBeTrue();
});
