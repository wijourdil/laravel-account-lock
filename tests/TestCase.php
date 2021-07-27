<?php

namespace Wijourdil\LaravelAccountLock\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use Wijourdil\LaravelAccountLock\LaravelAccountLockServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Wijourdil\\LaravelAccountLock\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelAccountLockServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('auth.providers', [
            'users' => [
                'driver' => 'eloquent',
                'model' => User::class,
            ],

            'admins' => [
                'driver' => 'database',
                'table' => 'admins',
            ],
        ]);
        config()->set('auth.guards', [
            'web' => [
                'driver' => 'session',
                'provider' => 'users',
            ],

            'admin' => [
                'driver' => 'session',
                'provider' => 'admins',
            ],

            'api' => [
                'driver' => 'token',
                'provider' => 'users',
                'hash' => false,
            ],
        ]);
    }
}
