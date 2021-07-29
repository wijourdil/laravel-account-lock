<?php

namespace Wijourdil\LaravelAccountLock\Tests\Feature\Http\Controllers;

use Orchestra\Testbench\Factories\UserFactory;
use Wijourdil\LaravelAccountLock\AccountLock;
use Wijourdil\LaravelAccountLock\Tests\TestCase;

class AccountLockControllerTest extends TestCase
{
    /** @test */
    public function it_locks_account_when_we_reach_the_url()
    {
        $user = UserFactory::new()->create();
        $url = (new AccountLock())->generateLockUrl($user, 60);

        $this->get($url)->assertSuccessful();

        $this->assertDatabaseHas('locked_accounts', [
            'authenticatable_table' => $user->getTable(),
            'authenticatable_id' => $user->getKey(),
            'is_locked' => true,
        ]);
    }

    /** @test */
    public function it_shows_an_error_if_the_url_is_malformed()
    {
        $user = UserFactory::new()->create();
        $malformedUrl = (new AccountLock())->generateLockUrl($user, 60) . 'aaa';

        $this->get($malformedUrl)->assertForbidden();

        $this->assertDatabaseCount('locked_accounts', 0);
    }

    /** @test */
    public function it_shows_an_error_if_the_url_has_expired()
    {
        $user = UserFactory::new()->create();
        $expiredUrl = (new AccountLock())->generateLockUrl($user, -60);

        $this->get($expiredUrl)->assertForbidden();

        $this->assertDatabaseCount('locked_accounts', 0);
    }

    /** @test */
    public function it_shows_a_not_found_error_if_the_url_contains_data_for_inexisting_user()
    {
        $user = UserFactory::new()->create();
        $url = (new AccountLock())->generateLockUrl($user, 60);

        $user->delete();

        $this->get($url)->assertNotFound();

        $this->assertDatabaseCount('locked_accounts', 0);
    }
}
