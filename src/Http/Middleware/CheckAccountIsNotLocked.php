<?php

namespace Wijourdil\LaravelAccountLock\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
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
                if ($request->expectsJson()) {
                    return new JsonResponse(
                        ['message' => __('account-lock::translations.json-error-account-locked')],
                        Response::HTTP_FORBIDDEN
                    );
                } else {
                    return response()->view('account-lock::account-locked', [], Response::HTTP_FORBIDDEN);
                }
            }
        }

        return $next($request);
    }

    private function someoneIsLoggedInAndLockedForGuard(string $guardName, array $guardConfig): bool
    {
        $authenticatedUser = Auth::guard($guardName)->user();

        return (null !== $authenticatedUser && $this->lockService->isLocked($authenticatedUser));
    }
}
