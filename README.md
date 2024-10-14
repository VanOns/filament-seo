# Filament SEO

Filament SEO is a package that provides a simple way to manage SEO tags for your Laravel application. It is built on top of the Filament Admin package and provides a simple way to manage SEO tags for your Laravel application.

## Installation

This package is not published on packagist.org, you must add it to your
`composer.json` as a repository manually:

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/VanOns/filament-cms"
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
