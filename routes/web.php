<?php

use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Route;
use Wijourdil\LaravelAccountLock\Http\Controllers\AccountLockController;

Route::get('account-lock/lock', [AccountLockController::class, 'lock'])
    ->middleware([
        ValidateSignature::class,
    ])
    ->name('lock-account');
