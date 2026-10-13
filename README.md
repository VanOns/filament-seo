<p align="center" class="filament-hidden"><img src="art/social-card.png" alt="Social card of Filament SEO"></p>

# Filament SEO

[![Latest version on GitHub](https://img.shields.io/github/release/VanOns/filament-seo.svg?style=flat-square)](https://github.com/VanOns/filament-seo/releases)
[![Total downloads](https://img.shields.io/packagist/dt/van-ons/filament-seo.svg?style=flat-square)](https://packagist.org/packages/van-ons/filament-seo)
[![GitHub issues](https://img.shields.io/github/issues/VanOns/filament-seo?style=flat-square)](https://github.com/VanOns/filament-seo/issues)
[![License](https://img.shields.io/github/license/VanOns/filament-seo?style=flat-square)](https://github.com/VanOns/filament-seo/blob/main/LICENSE.md)
[![Plumb score](https://img.shields.io/badge/dynamic/regex?url=https%3A%2F%2Fplumbphp.dev%2Fbadges%2Fvan-ons%2Ffilament-seo%2Fcomposite.svg&search=%3Ctitle%3Eplumb%3A%5Cs%2A%28%5B%5E%3C%5D%2B%29%3C&replace=%241&label=plumb&style=flat-square)](https://plumbphp.dev/van-ons/filament-seo)

Add SEO management to your Filament admin panel — meta tags for your models, made available in your app's layout.

## Quick start

> For Filament version compatibility, see [Compatibility](docs/compatibility.md).

### Installation

```bash
composer require van-ons/filament-seo:^1.0
```

### Model preparation

Add a `seo` column to your model's table:

```php
Schema::table('pages', function (Blueprint $table) {
    $table->json('seo')->nullable();
});
```

Implement the `Seoable` contract on your model:

```php
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

### Add fields to your Filament resource

```php
Filament\Forms\Components\Tabs\Tab::make('seo')
    ->schema(App\Models\Page::getSeoFields()),
```

### Add the view to your layout

```blade
<x-filament-seo::head />
```

## Documentation

Please see the [documentation](docs) for detailed information about installation and usage.

## Contributing

Please see [Contributing](CONTRIBUTING.md) for more information about how you can contribute.

## Testing

```bash
composer test
```

## Changelog

Please see [Changelog](CHANGELOG.md) for more information about what has changed recently.

## Upgrading

Please see [Upgrading](UPGRADING.md) for more information about how to upgrade.

## Security

Please see [Security](SECURITY.md) for more information about how we deal with security.

## Credits

We would like to thank the following contributors for their contributions to this project:

- [All contributors](../../contributors)

## License

The scripts and documentation in this project are released under the [MIT License](LICENSE.md).

---

<p align="center"><a href="https://van-ons.nl/" target="_blank"><img src="https://opensource.van-ons.nl/files/cow.png" width="50" alt="Logo of Van Ons"></a></p>
