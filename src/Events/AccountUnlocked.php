<?php

namespace Wijourdil\LaravelAccountLock\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(private Authenticatable $authenticatable)
    {
        //
    }

    public function getAuthenticatable(): Authenticatable
    {
        return $this->authenticatable;
    }
}
