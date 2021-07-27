<?php

namespace Wijourdil\LaravelAccountLock\Tests\Unit;

use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\Factories\UserFactory;
use Wijourdil\LaravelAccountLock\Exceptions\InexistingModelException;
use Wijourdil\LaravelAccountLock\LaravelAccountLock;
use Wijourdil\LaravelAccountLock\Models\LockedAccount;
use Wijourdil\LaravelAccountLock\Tests\TestCase;

class LaravelAccountLockTest extends TestCase
{
    /** @test */
    public function it_can_generate_an_url()
    {
        $user = UserFactory::new()->create();

        $this->assertStringStartsWith(
            route('lock-account'),
            (new LaravelAccountLock)->generateLockUrl($user->getTable(), $user->getKey(), 60)
        );
    }

    /** @test */
    public function it_can_lock_an_account()
    {
        $user = UserFactory::new()->create();
        $this->assertDatabaseCount('locked_accounts', 0);

        (new LaravelAccountLock)->lock($user->getTable(), $user->getKey());

        $this->assertDatabaseCount('locked_accounts', 1);
        $this->assertDatabaseHas('locked_accounts', [
            'authenticatable_table' => $user->getTable(),
            'authenticatable_id' => $user->getKey(),
            'is_locked' => true,
        ]);
    }

    /** @test */
    public function it_can_unlock_an_account()
    {
        $user = UserFactory::new()->create();

        LockedAccount::factory()->forModel($user)->locked()->create();

        (new LaravelAccountLock)->unlock($user->getTable(), $user->getKey());

        $this->assertDatabaseCount('locked_accounts', 1);
        $this->assertDatabaseHas('locked_accounts', [
            'authenticatable_table' => $user->getTable(),
            'authenticatable_id' => $user->getKey(),
            'is_locked' => false,
        ]);
    }

    /** @test */
    public function it_can_check_if_an_account_is_locked()
    {
        $user1 = UserFactory::new()->create();
        $user2 = UserFactory::new()->create();
        $user3 = UserFactory::new()->create();

        LockedAccount::factory()->forModel($user1)->locked()->create();
        LockedAccount::factory()->forModel($user2)->unlocked()->create();

        $this->assertTrue((new LaravelAccountLock)->isLocked('users', $user1->getKey()));
        $this->assertFalse((new LaravelAccountLock)->isLocked('users', $user2->getKey()));
        $this->assertFalse((new LaravelAccountLock)->isLocked('users', $user3->getKey()));
    }

    /** @test */
    public function it_throws_an_exception_when_trying_to_generate_an_url_for_inexisting_model()
    {
        $this->expectException(InexistingModelException::class);

        (new LaravelAccountLock)->generateLockUrl((new User)->getTable(), 0, 60);
    }

    /** @test */
    public function it_throws_an_exception_when_trying_to_lock_inexisting_model()
    {
        $this->expectException(InexistingModelException::class);

        (new LaravelAccountLock)->lock((new User)->getTable(), 0);
    }

    /** @test */
    public function it_throws_an_exception_when_trying_to_unlock_inexisting_model()
    {
        $this->expectException(InexistingModelException::class);

        (new LaravelAccountLock)->unlock((new User)->getTable(), 0);
    }

    /** @test */
    public function it_throws_an_exception_when_trying_to_check_if_inexisting_model_is_locked()
    {
        $this->expectException(InexistingModelException::class);

        (new LaravelAccountLock)->isLocked((new User)->getTable(), 0);
    }
}
