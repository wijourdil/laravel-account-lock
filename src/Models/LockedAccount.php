<?php

namespace Wijourdil\LaravelAccountLock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LockedAccount
 *
 * @package Wijourdil\LaravelAccountLock\Models
 *
 * @property string $authenticatable_table
 * @property int $authenticatable_id
 * @property bool $is_locked
 */
class LockedAccount extends Model
{
    use HasFactory;

    protected $table = 'locked_accounts';

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    protected $fillable = [
        'authenticatable_table',
        'authenticatable_id',
        'is_locked',
    ];
}
