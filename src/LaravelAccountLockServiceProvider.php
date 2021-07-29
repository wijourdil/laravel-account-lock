<?php

namespace Wijourdil\LaravelAccountLock;

use Illuminate\Routing\Router;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Wijourdil\LaravelAccountLock\Http\Middleware\CheckAccountIsNotLocked;

class LaravelAccountLockServiceProvider extends PackageServiceProvider
{
    public function register()
    {
        parent::register();

        $this->app->bind('laravel-account-lock', function ($app) {
            return new AccountLock();
        });
    }

    public function boot(): self
    {
        parent::boot();

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('account-not-locked', CheckAccountIsNotLocked::class);

        return $this;
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-account-lock')
            ->hasTranslations()
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_account-lock_table')
            ->hasRoute('web');
    }
}
