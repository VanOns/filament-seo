<?php

namespace VanOns\FilamentSeo;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use VanOns\FilamentSeo\Contracts\Seoable;
use VanOns\FilamentSeo\Contracts\SeoSettings;

class SeoService
{
    protected array $data = [];
    protected array $mergeData = [];

    public Seoable $seoable;

    public function __construct(protected SeoSettings $settings, protected SeoableRepository $repository)
    {
    }

    public function setSeoable(Seoable $seoable): void
    {
        $this->seoable = $seoable;
    }

    public function merge(array $data): self
    {
        $this->mergeData = $data;

        return $this;
    }

    public function getSeoRobots(): ?string
    {
        if ($this->settings->noindex()) {
            return 'noindex';
        }

        return $this->seoable->getSeoRobots();
    }

    public function data(): array
    {
        if (!isset($this->seoable)) {
            $this->seoable = $this->repository->getSeoable();
        }

        $data = [];

        if ($title = $this->seoable->getSeoTitle()) {
            $data['title'] = $title;
        }

        if ($canonical = $this->seoable->getSeoCanonical()) {
            $data['canonical'] = $canonical;
        }

        if ($description = $this->seoable->getSeoDescription()) {
            $data['description'] = $description;
        }

        if ($keywords = $this->seoable->getSeoKeywords()) {
            $data['keywords'] = $keywords;
        }

        if ($robots = $this->getSeoRobots()) {
            $data['robots'] = $robots;
        }

        if ($ogTitle = $this->seoable->getSeoOgTitle()) {
            $data['og:title'] = $ogTitle;
        }

        if ($ogDescription = $this->seoable->getSeoOgDescription()) {
            $data['og:description'] = $ogDescription;
        }

        if ($ogImage = $this->seoable->getSeoOgImage()) {
            $data['og:image'] = $ogImage->url;
            $data['og:image:type'] = $ogImage->mimeType;
            $data['og:image:width'] = $ogImage->width;
            $data['og:image:height'] = $ogImage->height;
        }

        if ($twitterTitle = $this->seoable->getSeoTwitterTitle()) {
            $data['twitter:title'] = $twitterTitle;
        }

        if ($twitterDescription = $this->seoable->getSeoTwitterDescription()) {
            $data['twitter:description'] = $twitterDescription;
        }

        if ($twitterImage = $this->seoable->getSeoTwitterImage()) {
            $data['twitter:image'] = $twitterImage->url;
        }

        if ($twitterCard = $this->seoable->getSeoTwitterCard()) {
            $data['twitter:card'] = $twitterCard;
        }

        return array_merge($data, $this->mergeData);
    }

    public function title(string $tag = 'title'): string
    {
        $parts = $this->data()[$tag] ?? [];

        $parts[] = $this->settings->title();
        $separator = $this->settings->titleSeparator();

        return implode(" {$separator} ", $parts);
    }

    public function ogTitle(): string
    {
        return $this->title('og:title');
    }

    public function twitterTitle(): string
    {
        return $this->title('twitter:title');
    }

    public function canonical(): ?string
    {
        return $this->data()['canonical'] ?? null;
    }

    public function metaTags(): array
    {
        return [
            ['name' => 'description', 'content' => Arr::get($this->data(), 'description')],
            ['name' => 'robots', 'content' => Arr::get($this->data(), 'robots')],
            ['name' => 'keywords', 'content' => Arr::get($this->data(), 'keywords')],
            ['property' => 'og:description', 'content' => Arr::get($this->data(), 'og:description')],
            ['property' => 'og:image', 'content' => Arr::get($this->data(), 'og:image')],
            ['property' => 'og:image:type', 'content' => Arr::get($this->data(), 'og:image:type')],
            ['property' => 'og:image:width', 'content' => Arr::get($this->data(), 'og:image:width')],
            ['property' => 'og:image:height', 'content' => Arr::get($this->data(), 'og:image:height')],
            ['name' => 'twitter:description', 'content' => Arr::get($this->data(), 'twitter:description')],
            ['name' => 'twitter:image', 'content' => Arr::get($this->data(), 'twitter:image')],
            ['name' => 'twitter:card', 'content' => Arr::get($this->data(), 'twitter:card')],
        ];
    }

    public function renderMetaTags(): HtmlString
    {
        return new HtmlString(
            collect($this->metaTags())
                ->map(function ($tag) {
                    $name = $tag['name'] ?? $tag['property'] ?? null;

                    return '<meta ' . collect($tag)->map(function ($value, $key) use ($name) {
                        if ($key === 'content') {
                            $value = View::getSection("seo.{$name}", $value);
                        }

                        return $key . '="' . $value . '"';
                    })->implode(' ') . '>';
                })
                ->implode("\n")
        );
    }
}
