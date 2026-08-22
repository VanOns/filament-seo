# Installation

Start by installing the package via Composer:

```bash
composer require van-ons/filament-seo:^1.0
```

### Model preparation

Your model must have a `seo` column. Add a migration:

```php
public function up(): void
{
    Schema::table('pages', function (Blueprint $table) {
        $table->json('seo')->nullable();
    });
}

public function down(): void
{
    Schema::table('pages', function (Blueprint $table) {
        $table->dropColumn('seo');
    });
}
```

Your model must implement the `VanOns\FilamentSeo\Contracts\Seoable` contract. Use the `HasSeoFields` trait for sensible defaults. You must also implement `url(): string` yourself:

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

### Customizing the config

Publish the config file:

```bash
php artisan vendor:publish --tag=config
```
