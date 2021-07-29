<?php

namespace Wijourdil\LaravelAccountLock\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Wijourdil\LaravelAccountLock\AccountLock;

class CheckAccountIsNotLocked
{
    public function __construct(private AccountLock $lockService)
    {
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        foreach (config('auth.guards') as $guardName => $guardConfig) {
            if ($this->someoneIsLoggedInAndLockedForGuard($guardName, $guardConfig)) {
                abort(Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }

    private function someoneIsLoggedInAndLockedForGuard(string $guardName, array $guardConfig): bool
    {
        $authenticatedUserId = Auth::guard($guardName)->id();

        return
            null !== $authenticatedUserId &&
            $this->lockService->isLocked(
                $this->lockService->tableNameForAuthProvider($guardConfig['provider']),
                (int)$authenticatedUserId
            );
    }
}
