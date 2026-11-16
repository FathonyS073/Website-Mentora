<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Identity;

use App\Models\User;
use App\Modules\Identity\Domain\Contracts\AccountReader;
use App\Modules\Identity\Domain\ValueObjects\AccountSummary;
use App\Modules\Identity\Infrastructure\Persistence\EloquentAccountReader;
use Tests\TestCase;

final class AccountSummaryRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Keep the focused HTTP tests independent from PostgreSQL session storage.
        config()->set('session.driver', 'array');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('member.account.summary'))
            ->assertRedirect(route('login'));
    }

    public function test_identity_provider_binds_account_reader(): void
    {
        $this->assertInstanceOf(
            EloquentAccountReader::class,
            $this->app->make(AccountReader::class)
        );
    }

    public function test_verified_user_receives_account_summary(): void
    {
        $reader = $this->createMock(AccountReader::class);
        $reader->expects($this->once())
            ->method('findById')
            ->with(42)
            ->willReturn(new AccountSummary('42', 'MENTORA Tester', 'tester@example.test'));

        $this->app->instance(AccountReader::class, $reader);

        $user = new User();
        $user->id = 42;
        $user->name = 'MENTORA Tester';
        $user->email = 'tester@example.test';
        $user->email_verified_at = now();

        $this->actingAs($user)
            ->getJson(route('member.account.summary'))
            ->assertOk()
            ->assertJsonPath('data.id', '42')
            ->assertJsonPath('data.name', 'MENTORA Tester')
            ->assertJsonPath('data.email', 'tester@example.test');
    }
}