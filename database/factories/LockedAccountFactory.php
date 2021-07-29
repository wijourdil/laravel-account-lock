<?php

namespace Wijourdil\LaravelAccountLock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\Factories\UserFactory;
use Wijourdil\LaravelAccountLock\AccountLock;
use Wijourdil\LaravelAccountLock\Models\LockedAccount;

class LockedAccountFactory extends Factory
{
    protected $model = LockedAccount::class;

    public function definition()
    {
        return [
            'authenticatable_table' => (new AccountLock)->tableNameForAuthProvider('users'),
            'authenticatable_id' => UserFactory::new(),
            'is_locked' => false,
        ];
    }

    public function forModel(Model $model): self
    {
        return $this->state([
            'authenticatable_table' => $model->getTable(),
            'authenticatable_id' => $model->getKey(),
        ]);
    }

    public function locked(): self
    {
        return $this->state([
            'is_locked' => true,
        ]);
    }

    public function unlocked(): self
    {
        return $this->state([
            'is_locked' => false,
        ]);
    }
}

