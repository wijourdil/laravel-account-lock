<?php

use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Route;
use Wijourdil\LaravelAccountLock\Http\Controllers\LaravelAccountLockController;

Route::get('account-lock/lock', [LaravelAccountLockController::class, 'lock'])
    ->middleware([
        ValidateSignature::class,
    ])
    ->name('lock-account');
