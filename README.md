# 🔒 Laravel Account Lock via URL

[![Latest Version on Packagist](https://img.shields.io/packagist/v/wijourdil/laravel-account-lock.svg?style=flat)](https://packagist.org/packages/wijourdil/laravel-account-lock)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/wijourdil/laravel-account-lock/run-tests?label=tests)](https://github.com/wijourdil/laravel-account-lock/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/wijourdil/laravel-account-lock/Check%20&%20fix%20styling?label=code%20style)](https://github.com/wijourdil/laravel-account-lock/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/wijourdil/laravel-account-lock.svg?style=flat)](https://packagist.org/packages/wijourdil/laravel-account-lock)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/wijourdil/laravel-account-lock)
![Packagist License](https://img.shields.io/packagist/l/wijourdil/laravel-account-lock)
[![gitmoji.dev](https://img.shields.io/badge/gitmoji-%20😜%20😍-FFDD67.svg?style=flat)](https://gitmoji.dev)

---

[comment]: <> (This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.)
This package helps you to easily lock users accounts via links in emails.

[comment]: <> (TODO : ajouter ici un exemple avec des images ?)

## Requirements & Compatibility

* PHP ^8.0
* Laravel 8

## Installation

You can install the package via composer:

```bash
composer require wijourdil/laravel-account-lock
```

Publish all the files (config, migrations, translations, views) with:

```bash
php artisan vendor:publish --provider="Wijourdil\LaravelAccountLock\LaravelAccountLockServiceProvider"
```

Then run the migrations with:

```bash
php artisan migrate
```

## Configuration & Customization

### Configuration file

See the published config file `config/account-lock.php` to customize available configuration.

### Blade templates

See the published files in the folder `resources/views/vendor/account-lock`

To customize the page displayed to a user when he locks successfully his account, you can edit this
file: `account-locked.blade.php`

ℹ️ It is recommended to check the `mail` variable used in the published template 

### Strings translations

See the published files in the folder `resources/lang/vendor/account-lock`

## Usage

### Apply the middleware to your routes

This package comes with a middleware returning a 403 HTTP status if the current authenticated user tries to access a
protected route.

Use the `account-not-locked` middleware on the routes you want to protect:

```php
Route::middleware('account-not-locked')->group(function () {
    // Your routes here
});
```

### Use the service class

If you want to use the service class, you can either instantiate it yourself or use it like a Facade, depending on your
preferences.

If you want to instantiate the service / use dependency injection, you must import the class:

```php
use Wijourdil\LaravelAccountLock\AccountLock;

$url = (new AccountLock)->generateLockUrl(...);
```

If you want to use it like a Facade, you just have to call the methods without importing nothing, like:

```php
$url = AccountLock::generateLockUrl(...);
```

## Available methods

### Generate an URL to lock account

```php
$user = Auth::user();
$expirationInMinutes = 60 * 24 * 7; // 1 week

$lockUrl = AccountLock::generateLockUrl($user, $expirationInMinutes);
```

### Manually lock an account

```php
$user = Auth::user();

AccountLock::lock($user);
```

### Manually unlock an account

```php
$user = Auth::user();

AccountLock::unlock($user);
```

### Check if an account is locked

```php
$user = Auth::user();

AccountLock::isLocked($user);
```

## Testing

Run the tests without code coverage:

```shell
composer test
```

Run the tests with code coverage:

```shell
composer test-coverage
```

Run phpstan code analysis:

```shell
composer stan
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Wilfried Jourdil](https://github.com/wijourdil)

[comment]: <> (- [All Contributors]&#40;../../contributors&#41;)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
