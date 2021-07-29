<?php

namespace Wijourdil\LaravelAccountLock\Tests\Unit;

use Illuminate\Auth\GenericUser;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Crypt;
use Orchestra\Testbench\Factories\UserFactory;
use Wijourdil\LaravelAccountLock\AccountLock;
use Wijourdil\LaravelAccountLock\Exceptions\InexistingModelException;
use Wijourdil\LaravelAccountLock\Models\LockedAccount;
use Wijourdil\LaravelAccountLock\Tests\TestCase;

class AccountLockTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        User::unguard();
    }

    /** @test */
    public function it_can_generate_an_url_for_a_model()
    {
        $user = UserFactory::new()->create();

        $generatedUrl = (new AccountLock())->generateLockUrl($user);

        $this->assertStringStartsWith(route('lock-account'), $generatedUrl);

        $generatedUrlQueryString = parse_url($generatedUrl, PHP_URL_QUERY);

        parse_str($generatedUrlQueryString, $output);
        $data = Crypt::decrypt($output['data']);

        $this->assertEquals($user->getTable(), $data['table']);
        $this->assertEquals($user->getKeyName(), $data['identifierName']);
        $this->assertEquals($user->getKey(), $data['identifierValue']);
        $this->assertEquals(User::class, $data['type']);
    }

    /** @test */
    public function it_can_generate_an_url_for_a_generic_user()
    {
        $user = new GenericUser(
            UserFactory::new()->create()->toArray()
        );

        $generatedUrl = (new AccountLock())->generateLockUrl($user, 60);

        $this->assertStringStartsWith(route('lock-account'), $generatedUrl);

        $generatedUrlQueryString = parse_url($generatedUrl, PHP_URL_QUERY);

        parse_str($generatedUrlQueryString, $output);
        $data = Crypt::decrypt($output['data']);

        $this->assertEquals('users', $data['table']);
        $this->assertEquals($user->getAuthIdentifierName(), $data['identifierName']);
        $this->assertEquals($user->getAuthIdentifier(), $data['identifierValue']);
        $this->assertEquals(GenericUser::class, $data['type']);
    }

    /** @test */
    public function it_can_lock_an_account()
    {
        $user = UserFactory::new()->create();
        $this->assertDatabaseCount('locked_accounts', 0);

        (new AccountLock())->lock($user);

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

        (new AccountLock())->unlock($user);

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

        $this->assertTrue((new AccountLock())->isLocked($user1));
        $this->assertFalse((new AccountLock())->isLocked($user2));
        $this->assertFalse((new AccountLock())->isLocked($user3));
    }

    /** @test */
    public function it_throws_an_exception_when_trying_to_generate_an_url_for_inexisting_model()
    {
        $this->expectException(InexistingModelException::class);

        (new AccountLock())->generateLockUrl(new User(['id' => 0]), 60);
    }

    /** @test */
    public function it_throws_an_exception_when_trying_to_lock_inexisting_model()
    {
        $this->expectException(InexistingModelException::class);

        (new AccountLock())->lock(new User(['id' => 0]));
    }

    /** @test */
    public function it_throws_an_exception_when_trying_to_unlock_inexisting_model()
    {
        $this->expectException(InexistingModelException::class);

        (new AccountLock())->unlock(new User(['id' => 0]));
    }

    /** @test */
    public function it_throws_an_exception_when_trying_to_check_if_inexisting_model_is_locked()
    {
        $this->expectException(InexistingModelException::class);

        (new AccountLock())->isLocked(new User(['id' => 0]));
    }
}
