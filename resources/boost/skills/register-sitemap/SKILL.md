---
name: register-sitemap
description: Register XML sitemaps with van-ons/filament-seo so search engines can discover model URLs. Covers Sitemap::fromQuery / fromModel / static-URL forms, the auto-registered route names and URL patterns, pagination behavior at perPage > 1000, and the SEO_SITEMAP_ENABLED toggle. Use when the user says "add a sitemap", "register sitemap for <model>", "expose /sitemap.xml", or asks how filament-seo's sitemap generation works.
---

## When to use

The user wants `/sitemap.xml` and per-resource sitemap files served by their Laravel app. Skip this skill if they only want SEO meta tags on individual pages — that's `add-seo-to-model`.

## Prerequisites

Models that should appear in a sitemap must implement `Contracts\Sitemapable`. The `HasSeoFields` trait already implements it via `toSitemapUrl()`, so any model from the `add-seo-to-model` skill is sitemap-ready.

For models that don't use `HasSeoFields`, implement `toSitemapUrl(): ?SitemapUrl` directly and return `null` to skip a record.

## Routes

The service provider registers three routes automatically (gated on `seo.sitemap.routes_enabled`, env `SEO_SITEMAP_ENABLED`, default `true`):

| Route name | URL | Returns |
| --- | --- | --- |
| `sitemap.index` | `/sitemap.xml` | Index of registered sitemap slugs. |
| `sitemap.show` | `/{slug}-sitemap.xml` | A single sitemap (used when its `count <= perPage`). |
| `sitemap.page.show` | `/{slug}-sitemap-{page}.xml` | One page of a paginated sitemap. |

URL pattern is `{slug}-sitemap.xml`, **not** `/sitemaps/{slug}.xml`. (The README is wrong on this; the routes file is authoritative.)

## Workflow

### Register sitemaps in a service provider

`SitemapRegistry::register()` is additive and merges into the registry, so you can call it from multiple service providers.

```php
// app/Providers/AppServiceProvider.php
use App\Models\Article;
use App\Models\Page;
use VanOns\FilamentSeo\Facades\SitemapRegistry;
use VanOns\FilamentSeo\Sitemap\Sitemap;
use VanOns\FilamentSeo\Sitemap\SitemapUrl;

public function boot(): void
{
    SitemapRegistry::register([
        // From an Eloquent query — applies scopes/filters before generation.
        Sitemap::fromQuery('pages', Page::published()->orderBy('updated_at', 'desc')),

        // From a model class — equivalent to Model::query() with no filters.
        Sitemap::fromModel('articles', Article::class),

        // Static URL list — for hand-curated entries.
        new Sitemap(
            slug: 'home',
            urls: collect([
                new SitemapUrl(loc: url('/'), changefreq: 'weekly', priority: 1.0),
                new SitemapUrl(loc: url('/about'), changefreq: 'monthly', priority: 0.8),
            ]),
        ),
    ]);
}
```

### Choosing the form

| Form | Use when |
| --- | --- |
| `Sitemap::fromQuery($slug, $builder)` | You need scopes (`->published()`, `->whereNotNull('url')`, soft-delete handling) or a specific order. **Most common.** |
| `Sitemap::fromModel($slug, $modelClass)` | Every row of the table belongs in the sitemap, no filters. |
| `new Sitemap(slug:, urls: collect([...]))` | Static URLs (homepage, contact, etc.) or sources outside Eloquent. |

### Constructor signature

```php
new Sitemap(
    string $slug,
    ?Collection $urls = null,
    ?Builder $query = null,
    ?int $perPage = 1000,
)
```

`perPage` defaults to 1000. When a sitemap's total `count` exceeds `perPage`, `Sitemap::url()` switches to the paginated `sitemap.page.show` route automatically and the index references each page.

### `SitemapUrl` fields

```php
new SitemapUrl(
    string $loc,           // absolute URL — required
    ?string $lastmod = null, // 'Y-m-d'
    ?string $changefreq = null, // 'always' | 'hourly' | 'daily' | 'weekly' | 'monthly' | 'yearly' | 'never'
    ?float $priority = null, // 0.0 — 1.0
)
```

Models using `HasSeoFields` populate `lastmod` from `updated_at`, and `changefreq` / `priority` from the `seo.change_frequency` and `seo.priority` JSON keys (set in the Filament Sitemap section).

### Disable

```dotenv
SEO_SITEMAP_ENABLED=false
```

Routes are skipped entirely; the registry still works for any code that resolves it.

## Common follow-ups

- **`robots.txt` reference** — point search engines at the index:
  ```
  Sitemap: https://example.com/sitemap.xml
  ```
- **Excluding records** — return `null` from `toSitemapUrl()` (or have the model's `url()` return `null` — `HasSeoFields::toSitemapUrl()` checks for that and returns `null`).
- **Custom XML output** — the views are publishable: `php artisan vendor:publish --tag=filament-seo-views`. Edit `resources/views/vendor/filament-seo/sitemap/{index,show}.blade.php`.

## Anti-patterns

- **Don't register sitemaps inside `register()`.** The registry is bound there but not yet ready; use `boot()`.
- **Don't pass route paths as slugs.** Slugs become path segments — keep them URL-safe (`pages`, `blog-articles`), not `pages.xml` or `/pages`.
- **Don't pass `Page::published()->get()` to `Sitemap::fromQuery()`.** It expects an unevaluated `Builder` — pagination at large datasets relies on it. Pass the builder, not the collection.
- **Don't expect ordered output without ordering the query.** `fromQuery` runs `skip()->take()->get()` per page; without an `orderBy()` you'll get inconsistent paginated chunks across requests.
