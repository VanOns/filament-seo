<?php

namespace VanOns\FilamentSeo\Analysis;

use Closure;
use Illuminate\Support\Collection;
use VanOns\FilamentSeo\Contracts\Seoable;

class AnalyserRegistry
{
    /**
     * @var Collection<AnalyserInterface>
     */
    protected Collection $analysers;

    protected Closure $filterCallback;

    public function __construct()
    {
        $this->analysers = collect();

        $this->register(config('seo.analysis.default_analysers', []));
    }

    /**
     * @param array<AnalyserInterface|string> $analysers
     * @return void
     */
    public function register(array $analysers): void
    {
        $this->analysers = $this->analysers->merge(
            collect($analysers)->map(
                fn (AnalyserInterface|string $analyser) => is_string($analyser) ? new $analyser() : $analyser
            )
        );
    }

    /**
     * @param callable $callback
     * @return void
     */
    public function unregister(callable $callback): void
    {
        $this->analysers = $this->analysers->reject(
            fn (AnalyserInterface $analyser) => $callback($analyser)
        );
    }

    public function getAnalysers(Seoable|null $seoable = null): array
    {
        if (isset($this->filterCallback)) {
            return ($this->filterCallback)($this->analysers->all(), $seoable);
        }

        return $this->analysers->all();
    }

    public function filter(Closure $callback): static
    {
        $this->filterCallback = $callback;

        return $this;
    }
}
