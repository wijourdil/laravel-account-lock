<?php

namespace Wijourdil\LaravelAccountLock;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use InvalidArgumentException;
use Wijourdil\LaravelAccountLock\Classes\Account;
use Wijourdil\LaravelAccountLock\Exceptions\InexistingModelException;

class AccountLock
{
    /**
     * @throws InexistingModelException
     * @throws InvalidArgumentException
     */
    public function generateLockUrl(Authenticatable $authenticatable, int $expiresInMinutes = null): string
    {
        $account = $this->getAccount($authenticatable);

        if (null === $expiresInMinutes) {
            (int)$expiresInMinutes = config('account-lock.url-lifetime-in-minutes');
        }

        $data = Crypt::encrypt($account->toArray());

        return URL::temporarySignedRoute(
            'lock-account',
            now()->addMinutes($expiresInMinutes),
            ['data' => $data],
        );
    }

    /**
     * @throws InexistingModelException
     * @throws InvalidArgumentException
     */
    public function isLocked(Authenticatable $authenticatable): bool
    {
        $account = $this->getAccount($authenticatable);

        return DB::table('locked_accounts')
            ->where('authenticatable_table', '=', $account->getTable())
            ->where('authenticatable_id', '=', $account->getIdentifierValue())
            ->where('is_locked', '=', true)
            ->exists();
    }

    /**
     * @throws InexistingModelException
     * @throws InvalidArgumentException
     */
    public function lock(Authenticatable $authenticatable): void
    {
        $account = $this->getAccount($authenticatable);

        DB::table('locked_accounts')->updateOrInsert(
            [
                'authenticatable_table' => $account->getTable(),
                'authenticatable_id' => $account->getIdentifierValue(),
            ],
            [
                'is_locked' => true,
                'locked_at' => now(),
            ]
        );
    }

    /**
     * @throws InexistingModelException
     * @throws InvalidArgumentException
     */
    public function unlock(Authenticatable $authenticatable): void
    {
        $account = $this->getAccount($authenticatable);

        DB::table('locked_accounts')
            ->where('authenticatable_table', '=', $account->getTable())
            ->where('authenticatable_id', '=', $account->getIdentifierValue())
            ->update([
                'is_locked' => false,
                'unlocked_at' => now(),
            ]);
    }

    /**
     * @throws InexistingModelException
     * @throws InvalidArgumentException
     */
    private function getAccount(Authenticatable $authenticatable): Account
    {
        $table = $this->getTableNameForAuthenticatable($authenticatable);

        $this->protectAgainstInexistingAuthenticatable($table, $authenticatable->getAuthIdentifier());

        return new Account(
            table: $table,
            identifierName: $authenticatable->getAuthIdentifierName(),
            identifierValue: $authenticatable->getAuthIdentifier(),
            type: $authenticatable::class,
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getTableNameForAuthenticatable(Authenticatable $authenticatable): string
    {
        if ($authenticatable instanceof Model) {
            return $authenticatable->getTable();
        }

        foreach (config('auth.guards') as $guardConfig) {
            $retrievedUser = Auth::createUserProvider($guardConfig['provider'])?->retrieveByCredentials([
                $authenticatable->getRememberTokenName() => $authenticatable->getRememberToken(),
                $authenticatable->getAuthIdentifierName() => $authenticatable->getAuthIdentifier(),
            ]);

            if (null !== $retrievedUser) {
                return table_name_for_auth_provider($guardConfig['provider']);
            }
        }

        throw new InvalidArgumentException(
            "Unable to determine the table to use for the Authenticatable " . $authenticatable::class .
            " #{$authenticatable->getAuthIdentifier()}"
        );
    }

    /**
     * @throws InexistingModelException
     */
    private function protectAgainstInexistingAuthenticatable(string $table, int $id): void
    {
        if (empty(DB::table($table)->find($id))) {
            throw new InexistingModelException();
        }
    }
}
