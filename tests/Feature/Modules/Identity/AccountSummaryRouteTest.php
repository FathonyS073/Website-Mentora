<?php

declare(strict_types=1);

use App\Models\User;
use App\Modules\Identity\Domain\Contracts\AccountReader;
use App\Modules\Identity\Domain\ValueObjects\AccountSummary;
use App\Modules\Identity\Infrastructure\Persistence\EloquentAccountReader;

beforeEach(function (): void {
    // Do not depend on PostgreSQL-backed session storage in this focused test.
    config()->set('session.driver', 'array');
});

it('protects the identity summary route from guests', function (): void {
    $this->get(route('member.account.summary'))
        ->assertRedirect(route('login'));
});

it('resolves the account reader through the identity module provider', function (): void {
    expect(app(AccountReader::class))->toBeInstanceOf(EloquentAccountReader::class);
});

it('returns a verified users account summary through the application layer', function (): void {
    $reader = Mockery::mock(AccountReader::class);
    $reader->shouldReceive('findById')
        ->once()
        ->with(42)
        ->andReturn(new AccountSummary('42', 'MENTORA Tester', 'tester@example.test'));
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
        ->assertJsonPath('data.name', 'MENTORA Tester');
});
