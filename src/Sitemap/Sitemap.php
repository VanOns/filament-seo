<?php

namespace VanOns\FilamentSeo\Sitemap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use VanOns\FilamentSeo\Contracts\Sitemapable;

class Sitemap
{
    protected int $count;

    /**
     * @param Collection<SitemapUrl>|null $urls
     */
    public function __construct(
        public string $slug,
        public ?Collection $urls = null,
        public ?Builder $query = null,
        public ?int $perPage = 1000,
    ) {
        $this->perPage = max(1, $perPage ?? 1000);
    }

    public function url(int $page = 1): string
    {
        if ($this->pages() > 1) {
            return route('sitemap.page.show', ['sitemap' => $this->slug, 'page' => $page]);
        }

        return route('sitemap.show', ['sitemap' => $this->slug]);
    }

    /**
     * @return Collection<SitemapUrl>
     */
    public function urls(int $page = 1): Collection
    {
        if ($this->urls) {
            return $this->urls->skip(($page - 1) * $this->perPage)->take($this->perPage);
        }

        if ($this->query) {
            return $this->query->skip(($page - 1) * $this->perPage)->take($this->perPage)->get()->map(
                // @phpstan-ignore-next-line
                function (Sitemapable $seoable) {
                    return $seoable->toSitemapUrl();
                }
            )->filter()->values();
        }

        return collect();
    }

    public function count(): int
    {
        if (isset($this->count)) {
            return $this->count;
        }

        if ($this->urls) {
            $this->count = $this->urls->count();
        }

        if ($this->query) {
            $this->count =  $this->query->count();
        }

        return $this->count ?? 0;
    }

    public function pages(): int
    {
        return (int) ceil($this->count() / $this->perPage);
    }

    public static function fromQuery(string $slug, Builder $query): Sitemap
    {
        return new Sitemap(
            slug: $slug,
            query: $query
        );
    }

    public static function fromModel(string $slug, string $modelClass): Sitemap
    {
        return self::fromQuery(
            $slug,
            $modelClass::query()
        );
    }
}
