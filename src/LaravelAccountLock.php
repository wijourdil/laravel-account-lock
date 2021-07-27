<?php

namespace Wijourdil\LaravelAccountLock;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use InvalidArgumentException;
use Wijourdil\LaravelAccountLock\Exceptions\InexistingModelException;

class LaravelAccountLock
{
    /**
     * @throws InexistingModelException
     */
    public function generateLockUrl(string $table, int $id, int $expiresInMinutes = null): string
    {
        $this->protectAgainstInexistingAuthenticatable($table, $id);

        if (null === $expiresInMinutes) {
            (int)$expiresInMinutes = config('account-lock.url-lifetime-in-minutes');
        }

        $data = Crypt::encrypt([
            'table' => $table,
            'id' => $id,
        ]);

        return URL::temporarySignedRoute(
            'lock-account',
            now()->addMinutes($expiresInMinutes),
            ['data' => $data],
        );
    }

    /**
     * @throws InexistingModelException
     */
    public function isLocked(string $table, int $id): bool
    {
        $this->protectAgainstInexistingAuthenticatable($table, $id);

        return DB::table('locked_accounts')
            ->where('authenticatable_table', '=', $table)
            ->where('authenticatable_id', '=', $id)
            ->where('is_locked', '=', true)
            ->exists();
    }

    /**
     * @throws InexistingModelException
     */
    public function lock(string $table, int $id): void
    {
        $this->protectAgainstInexistingAuthenticatable($table, $id);

        DB::table('locked_accounts')->updateOrInsert(
            [
                'authenticatable_table' => $table,
                'authenticatable_id' => $id,
            ],
            [
                'is_locked' => true,
                'locked_at' => now(),
            ]
        );
    }

    /**
     * @throws InexistingModelException
     */
    public function unlock(string $table, int $id): void
    {
        $this->protectAgainstInexistingAuthenticatable($table, $id);

        DB::table('locked_accounts')
            ->where('authenticatable_table', '=', $table)
            ->where('authenticatable_id', '=', $id)
            ->update([
                'is_locked' => false,
                'unlocked_at' => now(),
            ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function tableNameForAuthProvider(string $provider): string
    {
        $config = config("auth.providers.$provider");

        if (empty($config)) {
            throw new InvalidArgumentException("Auth provider '$provider' cannot be found in config('auth.providers')");
        }

        if (isset($config['table'])) {
            return $config['table'];
        } elseif (isset($config['model'])) {
            $class = $config['model'];

            return (new $class())->getTable();
        } else {
            throw new InvalidArgumentException("Auth provider '$provider' does not have table or model defined.");
        }
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
