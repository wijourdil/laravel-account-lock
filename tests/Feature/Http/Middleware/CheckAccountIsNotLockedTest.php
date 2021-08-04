<?php

namespace Wijourdil\LaravelAccountLock\Tests\Feature\Http\Middleware;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Factories\UserFactory;
use Wijourdil\LaravelAccountLock\Http\Middleware\CheckAccountIsNotLocked;
use Wijourdil\LaravelAccountLock\Models\LockedAccount;
use Wijourdil\LaravelAccountLock\Tests\Factories\AdminFactory;
use Wijourdil\LaravelAccountLock\Tests\TestCase;

class CheckAccountIsNotLockedTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::get('test', fn () => new Response())->middleware(CheckAccountIsNotLocked::class);
    }

    /** @test */
    public function it_does_not_block_if_no_user_is_logged_in()
    {
        $this->get('/test')->assertSuccessful();
    }

    /** @test */
    public function it_returns_json_response_if_request_expects_json()
    {
        $user = UserFactory::new()->create();
        LockedAccount::factory()->forModel($user)->locked()->create();

        $this->be($user, 'api')
            ->getJson('/test')
            ->assertForbidden()
            ->assertExactJson([
                'message' => __('account-lock::translations.json-error-account-locked'),
            ]);
    }

    /**
     * @test
     * @dataProvider guardsDataProvider
     */
    public function it_does_not_block_if_logged_in_user_account_is_not_locked(string $factory, string $guard)
    {
        $this->be($factory::new()->create(), $guard)
            ->get('/test')
        ->assertSuccessful();
    }

    /**
     * @test
     * @dataProvider guardsDataProvider
     */
    public function it_does_not_block_if_logged_in_user_account_was_unlocked(string $factory, string $guard)
    {
        $model = $factory::new()->create();
        LockedAccount::factory()->forModel($model)->unlocked()->create();

        $this->be($model, $guard)
            ->get('/test')
            ->assertSuccessful();
    }

    /**
     * @test
     * @dataProvider guardsDataProvider
     */
    public function it_does_block_if_logged_in_user_account_is_currently_locked(string $factory, string $guard)
    {
        $model = $factory::new()->create();
        LockedAccount::factory()->forModel($model)->locked()->create();

        $this->be($model, $guard)
            ->get('/test')
            ->assertForbidden();
    }

    public function guardsDataProvider(): array
    {
        return [
            [
                UserFactory::class,
                'web',
            ],
            [
                AdminFactory::class,
                'admin',
            ],
            [
                UserFactory::class,
                'api',
            ],
        ];
    }
}
