<?php

namespace Wijourdil\LaravelAccountLock\Facades;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Facade;

/**
 * Class AccountLock
 *
 * @package Wijourdil\LaravelAccountLock\Facades
 *
 * @method static string generateLockUrl(Authenticatable $authenticatable, int $expiresInMinutes = null)
 * @method static bool isLocked(Authenticatable $authenticatable)
 * @method static void lock(Authenticatable $authenticatable)
 * @method static void unlock(Authenticatable $authenticatable)
 */
class AccountLock extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-account-lock';
    }
}
