<?php

namespace Wijourdil\LaravelAccountLock\Facades;

use Illuminate\Support\Facades\Facade;

class AccountLockFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-account-lock';
    }
}
