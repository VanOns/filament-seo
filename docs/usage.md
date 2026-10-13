# Usage

## Add fields to your Filament resource

Add the SEO fields to your form using your model's static `getSeoFields()` method:

```php
Filament\Forms\Components\Tabs::make()
    ->tabs([
        ...your form,
        Filament\Forms\Components\Tabs\Tab::make('seo')->schema(App\Models\Page::getSeoFields()),
    ]),
```

## Add the view to your layout

Add the blade component to your app's layout file to make SEO data available in the `<head>`:

```blade
<x-filament-seo::head />
```

## Load SEO data for your views

Load the model's SEO data while routing. Two options:

**Option 1:** Use the `Seoable` attribute:

```php
use VanOns\FilamentSeo\Attributes\Seoable;

#[Seoable(Page::class)]
public function show(Page $page): View
{
    return view('page.show', ['page' => $page]);
}
```

**Option 2:** Call `setSeoable()` on the `SeoService`:

```php
use VanOns\FilamentSeo\SeoService;

public function show(Page $page): View
{
    app(SeoService::class)->setSeoable($page);

    return view('page.show', ['page' => $page]);
}
```

## SEO Analysis

The package includes default SEO analysis rules (keyword density, meta description length, header tags, and more). Register additional rules in one of three ways:

**Option 1:** Publish the config and add to `default_analysers`:

```bash
php artisan vendor:publish --tag=filament-seo-config
```

```php
// config/seo.php
'analysis' => [
    'default_analysers' => [
        TitleLength::class,
        DescriptionLength::class,
        ContentLength::class,
        FocusKeyphraseLength::class,
        FocusKeyphraseInContent::class,
        FocusKeyphraseInTitle::class,
        FocusKeyphraseInDescription::class,
        FocusKeyphraseInUrl::class,
        FocusKeyPhraseUnique::class,
        MyAnalyser::class,
    ],
],
```

**Option 2:** Register via `AnalyserRegistry` in a service provider:

```php
use VanOns\FilamentSeo\Facades\AnalyserRegistry;

public function boot(): void
{
    AnalyserRegistry::register([
        MyAnalyser::class,
        new MyAnalyser(),
    ]);
}
```

**Option 3:** Filter the default analysers dynamically per model:

```php
use VanOns\FilamentSeo\Facades\AnalyserRegistry;

public function boot(): void
{
    AnalyserRegistry::filter(function (array $analysers, \VanOns\FilamentSeo\Contracts\Seoable $seoable) {
        if ($seoable instanceof \App\Models\SpecialPage) {
            $analysers[] = SpecialPageAnalyser::class;
        }

        return $analysers;
    });
}
```

Custom analysers must implement `VanOns\FilamentSeo\SEO\Analysis\AnalyserInterface`.

## AI Metadata Generation

Generate SEO metadata using AI. Configure in `.env`:

```dotenv
SEO_AI_ENABLED=true
SEO_AI_PROVIDER=openai
SEO_AI_MODEL=gpt-4o
OPENAI_API_KEY=your-key-here
```

PrismPHP handles AI provider communication. See the [PrismPHP documentation](https://prismphp.com) for compatible providers and models.

### Rate limiting

AI generation is rate limited per user (or per IP when no user is logged in). By default a user can generate 60 times per 60 seconds. Change this in `config/seo.php` or `.env`:

```dotenv
# Maximum number of generations per window
SEO_AI_RATE_LIMIT=60
# Length of the window in seconds
SEO_AI_RATE_LIMIT_DECAY_SECONDS=60
```

## Sitemaps

Your model automatically implements `Sitemapable` when it uses `HasSeoFields`. Register sitemaps in a service provider:

```php
use VanOns\FilamentSeo\Facades\SitemapRegistry;

public function boot(): void
{
    SitemapRegistry::register([
        Sitemap::fromQuery('pages', Page::published()),
        Sitemap::fromQuery('articles', Article::published()),
        new Sitemap(
            slug: 'home',
            urls: collect([new SitemapUrl(url('/'))])
        ),
    ]);
}
```

`Sitemap::fromModel()` lists every row of the table, including drafts. Use `fromQuery()` with a scope when a model has unpublished records.

Sitemaps are available at `/sitemap.xml` (index) and `/{slug}-sitemap.xml`. Disable via the config or `.env`:

```dotenv
SEO_SITEMAP_ENABLED=false
```

Instances set to `noindex` are left out of the sitemap. To keep them in:

```dotenv
SEO_SITEMAP_EXCLUDE_NOINDEX=false
```

## Middleware

The package registers middleware that adds `X-Robots-Tag: none` on non-production environments, preventing accidental indexing. No configuration required.

Conditions for the header to be added:
- `APP_ENV` is not `production`, **or**
- `seo.no-index` config value is `true` (set via `APP_SEO_NO_INDEX=true`)

To block indexing on production:

```dotenv
APP_SEO_NO_INDEX=true
```
