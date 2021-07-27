# This is my package LaravelAccountLock

[![Latest Version on Packagist](https://img.shields.io/packagist/v/wijourdil/laravel-account-lock.svg?style=flat-square)](https://packagist.org/packages/wijourdil/laravel-account-lock)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/wijourdil/laravel-account-lock/run-tests?label=tests)](https://github.com/wijourdil/laravel-account-lock/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/wijourdil/laravel-account-lock/Check%20&%20fix%20styling?label=code%20style)](https://github.com/wijourdil/laravel-account-lock/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/wijourdil/laravel-account-lock.svg?style=flat-square)](https://packagist.org/packages/wijourdil/laravel-account-lock)
<a href="https://gitmoji.dev">
<img src="https://img.shields.io/badge/gitmoji-%20😜%20😍-FFDD67.svg?style=flat-square" alt="Gitmoji">
</a>

---

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

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

## Usage

### Apply the middleware to your routes

This package comes with a middleware returning a 403 HTTP status if the current authenticated user tries to access a
protected route.

Use the `account-locked` middleware on the routes you want to protect:

```php
// web.php

Route::middleware('account-locked')->group(function () {
    // Your routes here
});
```

### Generate an URL to lock account

```php
$service = new Wijourdil\LaravelAccountLock\LaravelAccountLock();
$user = User::find(1);

$lockUrl = $service->generateLockUrl($user->getTable(), $user->getKey());
```

### Manually lock an account

```php
$service = new Wijourdil\LaravelAccountLock\LaravelAccountLock();
$user = User::find(1);

$service->lock($user->getTable(), $user->getKey());
```

### Manually unlock an account

```php
$service = new Wijourdil\LaravelAccountLock\LaravelAccountLock();
$user = User::find(1);

$service->unlock($user->getTable(), $user->getKey());
```

### Check if an account is locked

```php
$service = new Wijourdil\LaravelAccountLock\LaravelAccountLock();
$user = User::find(1);

$service->isLocked($user->getTable(), $user->getKey());
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Wilfried Jourdil](https://github.com/wijourdil)

<!--- [All Contributors](../../contributors)-->

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
