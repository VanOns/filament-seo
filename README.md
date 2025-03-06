# Filament SEO

Filament SEO is a package that provides a simple way to manage SEO tags for your Laravel application. It is built on top of the Filament Admin package and provides a simple way to manage SEO tags for your Laravel application.

## Installation

This package is not published on packagist.org, you must add it to your
`composer.json` as a repository manually:

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/VanOns/filament-seo"
        }
    ],
```

### Model preparation

Your model must have a seo column, add a migration to add the column to your model:

```php
public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('seo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('seo');
        });
    }
}
```

Your model must implement the `VanOns\FilamentSeo\Contracts\Seoable` contract,
the package provides the `VanOns\FilamentSeo\Contracts\Seoable` trait with sensible
defaults.

You must provide the url to your model yourself, as specified in the HasUrl contract:
`public function url(): string;`

#### Example

In the end your model should look something like this:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use VanOns\FilamentSeo\Concerns\HasSeoFields;
use VanOns\FilamentSeo\Contracts\Seoable;

class Page extends Model implements Seoable
{
    use HasSeoFields;

    public function url(): string
    {
        return route('page.show', $this);
    }
}
```

### Implement fields in Filament resource

You can add the SEO fields to your form using your model's static
`getSeoFields();` function.

An example:

```php
Filament\Forms\Components\Tabs::make()
    ->tabs([
        ...your form,
        Filament\Forms\Components\Tabs\Tab::make('seo')->schema(App\Models\Page::getSeoFields()),
    ]),
```

### Add the view to your layout

To make the SEO data available, add the blade component to your app's layout file:

```blade
<x-filament-seo::head />
```

### Load SEO data for your views

In order for the model's seo data to be show on a page, you must load it while
routing.

This can be done in two ways:

1. Use the `VanOns\FilamentSeo\Attributes\Seoable` attribute and give it
the class to load.
2. Load the `VanOns\FilamentSeo\SeoService` and call the `setSeoAble()` method
with the object to load the SEO attribute for.

```php
namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;
use VanOns\FilamentSeo\Attributes\Seoable;
use VanOns\FilamentSeo\SeoService;

class ShowPage extends Controller
{
    //option 1
    #[Seoable(Page::class)]
    public function show(Page $page): View
    {
        //option 2
        app(SeoService::class)->setSeoable($page);

        return view('page.show', [
            'page' => $page,
        ]);
    }
}

```

### Middleware

This package comes with middleware to prevent indexing by search engines for
non-production sites. The middleware is automatically loaded, no extra steps are
required.

The middleware can be found in `VanOns\FilamentSeo\Http\Middleware\NoIndex.php`.

The following checks are done before adding the
[X-Robots-Tag](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Robots-Tag)
with value `none`:

- Your `APP_ENV` is not equal to `production`.
- The config value `seo.no-index` is false, derived from `env(APP_SEO_NO_INDEX)`,

This should ensure non-production websites are never indexed.

To discourage search engine indexing on production, add `APP_SEO_NO_INDEX = true`
to your `.env` file, or change the value in the `config/seo.php` config file.
