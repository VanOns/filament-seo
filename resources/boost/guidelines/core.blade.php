## Filament SEO

`van-ons/filament-seo` adds SEO management to Filament 4+/5+ apps: a Filament form schema for SEO metadata stored in a per-model `seo` JSON column, a `<x-filament-seo::head />` Blade component that renders title/canonical/OG/Twitter meta tags, sitemap generation, content analysers, optional AI metadata generation via PrismPHP, and a global middleware that adds `X-Robots-Tag: none` outside production.

### Setup

A model becomes SEO-managed by adding a `seo` JSON column, implementing `VanOns\FilamentSeo\Contracts\Seoable`, using the `HasSeoFields` trait (which supplies defaults), and providing a `url()` method.

@verbatim
<code-snippet name="Seoable model" lang="php">
use Illuminate\Database\Eloquent\Model;
use VanOns\FilamentSeo\Concerns\HasSeoFields;
use VanOns\FilamentSeo\Contracts\Seoable;

class Page extends Model implements Seoable
{
    use HasSeoFields;

    protected $casts = ['seo' => 'array'];

    public function url(): ?string
    {
        return route('page.show', $this);
    }
}
</code-snippet>

<code-snippet name="Migration: seo JSON column" lang="php">
Schema::table('pages', function (Blueprint $table) {
    $table->json('seo')->nullable();
});
</code-snippet>

<code-snippet name="Filament form: SEO tab" lang="php">
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

Tabs::make()->tabs([
    // ...other tabs
    Tab::make('SEO')->schema(\App\Models\Page::getSeoFields()),
]);
</code-snippet>

<code-snippet name="Layout: render SEO tags" lang="blade">
<head>
    <x-filament-seo::head />
</head>
</code-snippet>

<code-snippet name="Frontend: load Seoable for the request" lang="php">
use VanOns\FilamentSeo\Attributes\Seoable;
use VanOns\FilamentSeo\SeoService;

class ShowPage
{
    // Option 1: attribute (resolves model from route binding or `column`+`parameter`)
    #[Seoable(Page::class)]
    public function show(Page $page)
    {
        // Option 2: imperative
        app(SeoService::class)->setSeoable($page);

        return view('page.show', ['page' => $page]);
    }
}
</code-snippet>
@endverbatim

### Public API

| Surface | Use |
| --- | --- |
| `Contracts\Seoable` | Interface every SEO-managed model implements (extends `HasUrl`, `Sitemapable`). |
| `Contracts\HasUrl` | `url(): ?string` — required for canonical + sitemap. |
| `Contracts\Sitemapable` | `toSitemapUrl(): ?SitemapUrl`. |
| `Contracts\SeoFields` | Bind in container to swap the form schema. Default impl: `Schemas\SeoFields`. |
| `Contracts\SeoSettings` | Bind to swap the global settings driver. Default impl: `Settings\ConfigSettingsDriver` (when `seo.settings.driver === 'config'`). |
| `Concerns\HasSeoFields` | Default getters for all `getSeo*()` methods + `static::getSeoFields()`. Reads from `seo.*` keys via `static::$seoPrefix`. |
| `Concerns\HasSeoGetters` | Null-returning defaults — used by `FallbackMap` and as a base for non-Eloquent `Seoable`s. |
| `Concerns\HasMetadataGeneration` | Wires the AI "generate" action onto a custom `Field`. |
| `Concerns\HasRecommendedLength` | `maxRecommendedLength(int)` setter for length-aware fields. |
| `Concerns\GetsFilledRecord` | Reconstitutes a `Seoable` from the live form state (handles create + relation manager). |
| `Schemas\SeoFields::make($prefix = 'seo.')` | Build the full SEO schema array. Override individual `*Field()` methods. |
| `Schemas\Analysis::make()` | Render analysers as a Filament schema. Used internally by `SeoFields`. |
| `Schemas\RecommendedLength::make()` | Length indicator schema (icon + `n / max`). |
| `Fields\SeoTitleInput` | `TextInput` subclass with length indicator + AI generate action. `->fallbackField('title')` controls the placeholder source. |
| `Fields\SeoDescriptionInput` | `Textarea` equivalent. `->fallbackField()` is null by default. |
| `Actions\GenerateMetadata` | Filament `Action` that runs a `Prompt` and dispatches the result via Alpine. |
| `Attributes\Seoable($model, $parameter = null, $column = null)` | Method-level attribute used by `SeoableRepository` to resolve the model for the current request. |
| `Components\Head` (Blade `<x-filament-seo::head />`) | Renders `<title>`, `<meta>` and `<link rel=canonical>` tags. |
| `Facades\AnalyserRegistry` | `register(array)`, `unregister(callable)`, `filter(Closure)`, `getAnalysers(?Seoable)`. |
| `Facades\SitemapRegistry` | `register(array $sitemaps)`, `getSitemaps()`, `getSitemap(string $slug)`. |
| `Sitemap\Sitemap` | `Sitemap::fromQuery($slug, $builder)`, `Sitemap::fromModel($slug, $modelClass)`, or `new Sitemap(slug: ..., urls: collect([...]))`. |
| `Sitemap\SitemapUrl` | DTO: `loc`, `lastmod`, `changefreq`, `priority`. |
| `SeoService` | Singleton. `setSeoable()`, `merge(array)`, `data()`, `metaTags()`, `renderMetaTags()`, `title()`, `canonical()`. |
| `SeoImage` | DTO: `url`, `width = 1200`, `height = 630`, `mimeType = 'image/jpeg'`. |
| `AI\Prompts\Prompt` | Abstract base. Subclasses: `GeneratePageTitle`, `GenerateDescription`. Output a `Prism\Prism\Structured\Response` with `structured['output']`. |
| `Analysis\AnalyserInterface` | `analyse(Seoable): ?AnalysisResult` — return `null` to skip. |
| `Analysis\AnalysisStatus` | `PASSED`, `WARNING`, `FAILED`, `INFO`. |
| `Http\Middleware\NoIndex` | Pushed globally in the service provider — no manual registration needed. |

### Default analysers

Configured in `config/seo.php` under `analysis.default_analysers`:

`TitleLength`, `DescriptionLength`, `ContentLength`, `FocusKeyphraseLength`, `FocusKeyphraseInContent`, `FocusKeyphraseInTitle`, `FocusKeyphraseInDescription`, `FocusKeyphraseInUrl`, `FocusKeyPhraseUnique`.

Note the irregular casing on `FocusKeyPhraseUnique` (capital `P`) — every other class spells it `Keyphrase`.

### Routes (auto-registered)

| Name | Path |
| --- | --- |
| `sitemap.index` | `/sitemap.xml` — index of registered sitemaps |
| `sitemap.show` | `/{sitemap}-sitemap.xml` |
| `sitemap.page.show` | `/{sitemap}-sitemap-{page}.xml` (paginated, when `count > perPage`) |

Disable with `SEO_SITEMAP_ENABLED=false`.

### Config keys

`config/seo.php` (publish with `php artisan vendor:publish --tag=filament-seo-config`):

| Key | Purpose |
| --- | --- |
| `settings.driver` | `'config'` is the only built-in driver. Set to `'database'` and bind your own `SeoSettings` impl to swap. |
| `settings.config.{title,title_separator,enable_suffix,og_image,noindex}` | Site-wide defaults used by `SeoService::title()` (suffix) and `getSeoRobots()`. |
| `fallback` | Class returned by `SeoableRepository` when no `Seoable` is bound. Default: `Maps\FallbackMap`. |
| `canonical.parameters` / `canonical.pagination_parameters` | Query params kept in `FallbackMap`'s canonical. Pagination param `page=1` is dropped. |
| `no-index` (env `APP_SEO_NO_INDEX`) | Forces `X-Robots-Tag: none` on production. Non-prod always gets the header. |
| `analysis.default_analysers` | Array of class strings or instances. Instances let you pass constructor args (e.g. `new FocusKeyPhraseUnique(uniqueQuery: fn ($q) => $q->where(...))`). |
| `ai.enabled` (env `SEO_AI_ENABLED`) | Master switch for AI generate buttons. |
| `ai.provider` (env `SEO_AI_PROVIDER`) | PrismPHP provider name (e.g. `openai`). |
| `ai.model` (env `SEO_AI_MODEL`) | PrismPHP model name. |
| `ai.rate_limit` (env `SEO_AI_RATE_LIMIT`) | Max AI generations per user per window, default `60`. |
| `ai.rate_limit_decay_seconds` (env `SEO_AI_RATE_LIMIT_DECAY_SECONDS`) | Rate limit window length in seconds, default `60`. |
| `sitemap.routes_enabled` (env `SEO_SITEMAP_ENABLED`) | Toggles route registration. |

### Conventions

- **Trait first.** Use `HasSeoFields` rather than implementing all `Seoable` getters by hand. Override only what differs (e.g. `getDefaultSeoTitleField()`, `getDefaultSeoDescription()`, `getDefaultSeoImage()`).
- **`seo` is JSON.** Cast it to `array` on the model. The trait reads via `Arr::get($this, 'seo.*')`, so the cast is what makes that work.
- **`url()` is required.** Returning `null` removes the model from sitemap output and produces a null canonical fallback.
- **`getSeoContent()` powers analysers + AI.** It defaults to `null` in `HasSeoGetters` — implement it on the model (e.g. return the body field) for `ContentLength`, `FocusKeyphraseInContent`, `GeneratePageTitle`, `GenerateDescription` to do anything useful.
- **Use the schema builder, not raw fields.** Prefer `Page::getSeoFields()` (resolves `Contracts\SeoFields` → `Schemas\SeoFields`) over hand-rolling the same Filament structure. Customize by binding your own `SeoFields` impl in a service provider, or by extending `Schemas\SeoFields` and overriding individual `*Field()` methods.
- **Register custom analysers via the facade**, not by appending to the config in a model. `AnalyserRegistry::filter(fn ($analysers, $seoable) => ...)` is the right hook for per-model analyser sets.
- **Sitemaps are pull-based.** `Sitemap::fromQuery()` and `fromModel()` lazily evaluate per-page; `new Sitemap(slug: ..., urls: collect([...]))` is for static URL lists.
- **AI generation is opt-in.** Without `SEO_AI_ENABLED=true` the generate buttons render no action (the closure returns `null`), and PrismPHP is not invoked.
- **`#[Seoable(...)]` resolution.** With no `parameter`, it matches the first route parameter that's an instance of the given model. With `parameter` + `column`, it queries `Model::where($column, $routeParam)->first()`.
- **Middleware is automatic.** Don't push `NoIndex` again — it's added globally in the service provider's `boot()`.
