---
name: add-seo-to-model
description: Make an existing Eloquent model SEO-managed via van-ons/filament-seo — add the seo JSON column, implement the Seoable contract, expose Filament SEO fields on its resource, and wire the frontend layout + controller so meta tags render. Use when the user says "add SEO to <model>", "make Page SEOable", "wire up filament-seo for <resource>", or asks how to integrate this package end-to-end.
---

## When to use

The user has the package installed and wants a specific model (Page, Article, Product, …) to:
- Store SEO metadata (title, description, keyphrase, OG tags, canonical, robots flags, sitemap fields) in a JSON column.
- Surface those fields in its Filament resource form.
- Render `<title>`, `<meta>`, and `<link rel=canonical>` on the public-facing route.

If the user only wants global site-wide defaults (no per-record fields) or only wants the sitemap, this is the wrong skill.

## Workflow

### 1. Add the `seo` JSON column

Generate a migration adding a nullable JSON column. Cast it to `array` on the model — the package's `HasSeoFields` trait reads via `Arr::get($this, 'seo.*')` and won't see a string-cast value.

```php
// database/migrations/xxxx_add_seo_to_pages_table.php
Schema::table('pages', function (Blueprint $table) {
    $table->json('seo')->nullable();
});
```

```php
// app/Models/Page.php
protected $casts = ['seo' => 'array'];
```

### 2. Implement `Seoable` and use `HasSeoFields`

```php
namespace App\Models;

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
```

`HasSeoFields` provides defaults for every `getSeo*()` method on the contract. Override only what you need to customize:

| Override | Default | When to override |
| --- | --- | --- |
| `getDefaultSeoTitleField(): string` | `'title'` | Source field name differs (e.g. `name`, `heading`). |
| `getDefaultSeoTitle(): ?string` | reads `static::getDefaultSeoTitleField()` | Need a computed fallback (concatenated, translated, etc.). |
| `getDefaultSeoDescription(): ?string` | `null` | Want a body-derived or excerpt-derived fallback. |
| `getDefaultSeoImage(): ?string` | `$this->featured_image ?? null` | Image lives elsewhere. |
| `getSeoContent(): ?string` | `null` (from `HasSeoGetters`) | **Almost always override.** Powers `ContentLength`, `FocusKeyphraseInContent`, and AI generation. Return the model's body/markdown/HTML. |
| `static $seoPrefix` | `'seo.'` | Storing under a different JSON key. |

`url()` must be implemented (it's on the `HasUrl` contract, not provided by the trait). Return `null` to exclude the record from sitemaps.

### 3. Add SEO fields to the Filament resource

```php
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

public static function form(Form $form): Form
{
    return $form->schema([
        Tabs::make()->tabs([
            Tab::make('Content')->schema([/* existing fields */]),
            Tab::make('SEO')->schema(\App\Models\Page::getSeoFields()),
        ]),
    ]);
}
```

`getSeoFields()` resolves `Contracts\SeoFields` from the container. The default impl (`Schemas\SeoFields`) renders a 3-column grid: Basic / Advanced / Social / Sitemap sections on the left, an Analysis panel on the right.

### 4. Add the head component to the public layout

```blade
{{-- resources/views/layouts/app.blade.php --}}
<head>
    <meta charset="utf-8">
    <x-filament-seo::head />
    {{-- ...rest of head --}}
</head>
```

### 5. Tell the package which model the current request is "about"

Either via the `#[Seoable]` attribute on the controller method, or imperatively via `SeoService::setSeoable()`:

```php
use VanOns\FilamentSeo\Attributes\Seoable;
use VanOns\FilamentSeo\SeoService;

class ShowPage extends Controller
{
    #[Seoable(Page::class)]
    public function show(Page $page)
    {
        // OR (and):
        // app(SeoService::class)->setSeoable($page);
        return view('page.show', compact('page'));
    }
}
```

#### Attribute resolution

| Form | Behavior |
| --- | --- |
| `#[Seoable(Page::class)]` | Picks the first route parameter that's an instance of `Page` (works with implicit route-model binding). |
| `#[Seoable(Page::class, parameter: 'slug', column: 'slug')]` | Runs `Page::where('slug', $route->parameter('slug'))->first()`. Use when the route param isn't a bound model. |

If neither resolution finds a `Seoable`, `SeoableRepository` falls back to `app(config('seo.fallback'))` (default: `Maps\FallbackMap`), which only emits a canonical URL.

### 6. Verify

After running the migration, in Tinker:

```php
$page = App\Models\Page::factory()->create();
$page->seo = ['title' => 'Hello', 'description' => 'Test'];
$page->save();
$page->getSeoTitle();        // ['Hello']
$page->getSeoDescription();  // 'Test'
$page->toSitemapUrl();       // SitemapUrl|null based on url()
```

Then visit the public route and confirm `<title>`, `<meta name="description">`, `<link rel="canonical">` and OG/Twitter tags render.

## Common follow-ups

- **Custom analyser** — implement `Analysis\AnalyserInterface::analyse(Seoable): ?AnalysisResult`, then either add the FQCN to `seo.analysis.default_analysers` or call `AnalyserRegistry::register([MyAnalyser::class])` in a service provider's `boot()`. Return `null` to skip silently. `AnalyserRegistry::filter(fn ($analysers, $seoable) => ...)` is the per-model hook.
- **AI metadata buttons** — set `SEO_AI_ENABLED=true`, `SEO_AI_PROVIDER=openai`, `SEO_AI_MODEL=gpt-4o`, plus the provider's API key (e.g. `OPENAI_API_KEY`). PrismPHP handles the call. The buttons appear next to the title/description fields and dispatch the result via Alpine.
- **Per-model SEO field overrides** — extend `Schemas\SeoFields` and override `titleField()` / `descriptionField()` / etc., then bind your subclass to `Contracts\SeoFields` in a service provider.

## Anti-patterns

- **Don't forget the `array` cast.** Without it, `Arr::get($this, 'seo.title')` returns `null` because `seo` is a string.
- **Don't push `NoIndex` middleware manually.** It's already added globally in `FilamentSeoServiceProvider::boot()`. Doing it twice gives you two `X-Robots-Tag` headers.
- **Don't hand-roll the SEO form.** `Page::getSeoFields()` already wires the live length indicators, the analysis panel, the AI-generate actions, and the canonical-url conditional field. Re-implementing those by hand drifts.
- **Don't return a query builder from `url()`.** It's called per-record from `toSitemapUrl()` and the canonical helper — return a string.
- **Don't store SEO content in a column that isn't `seo`** unless you set `static::$seoPrefix` on the model. The trait's getters all read via that prefix.
- **Don't expect `getSeoContent()` to return anything by default.** It returns `null` from `HasSeoGetters`, which silently disables `ContentLength`, `FocusKeyphraseInContent`, and AI generation. Always override it.
