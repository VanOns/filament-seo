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

## Middleware

The package registers middleware that adds `X-Robots-Tag: none` on non-production environments, preventing accidental indexing. No configuration required.

Conditions for the header to be added:
- `APP_ENV` is not `production`, **or**
- `seo.no-index` config value is `true` (set via `APP_SEO_NO_INDEX=true`)

To block indexing on production:

```dotenv
APP_SEO_NO_INDEX=true
```
