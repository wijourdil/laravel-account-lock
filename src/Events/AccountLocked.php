<?php

namespace Wijourdil\LaravelAccountLock\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountLocked
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(private Authenticatable $authenticatable)
    {
        //
    }

    public function getAuthenticatable(): Authenticatable
    {
        return $this->authenticatable;
    }
}
