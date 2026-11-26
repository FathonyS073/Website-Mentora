<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Identity;

use App\Models\User;
use App\Modules\Identity\Application\Services\PublicRegistrationRoleAssigner;
use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use Database\Seeders\RbacPermissionSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use InvalidArgumentException;
use Tests\TestCase;

/** Public registration may create only Member / Guardian users. */
final class PublicRegistrationRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacPermissionSeeder::class);
    }

    public function test_existing_registration_payload_still_creates_member(): void
    {
        $this->post(route('register.store'), $this->registrationData('member-legacy@example.test'))
            ->assertRedirect();

        $user = User::query()->where('email', 'member-legacy@example.test')->firstOrFail();
        $this->assertSame(['member'], app(RbacAccessReader::class)->rolesFor($user->id));
    }

    public function test_explicit_member_registration_has_only_member_permissions(): void
    {
        $this->post(route('register.store'), [
            ...$this->registrationData('member-explicit@example.test'),
            'account_type' => 'member',
        ])->assertRedirect();

        $user = User::query()->where('email', 'member-explicit@example.test')->firstOrFail();
        $access = app(RbacAccessReader::class);
        $this->assertSame(['member'], $access->rolesFor($user->id));
        $this->assertTrue($access->hasPermission($user->id, 'tracker.manage'));
        $this->assertFalse($access->hasPermission($user->id, 'guardian.progress.view'));
    }

    public function test_guardian_registers_with_only_guardian_role_and_permissions(): void
    {
        $this->post(route('register.store'), [
            ...$this->registrationData('guardian@example.test'),
            'account_type' => 'guardian',
        ])->assertRedirect();

        $user = User::query()->where('email', 'guardian@example.test')->firstOrFail();
        $access = app(RbacAccessReader::class);

        $this->assertSame(['guardian'], $access->rolesFor($user->id));
        $this->assertTrue($access->hasPermission($user->id, 'profile.view'));
        $this->assertTrue($access->hasPermission($user->id, 'guardian.links.view'));
        $this->assertFalse($access->hasPermission($user->id, 'tracker.manage'));
        $this->assertFalse($access->hasPermission($user->id, 'roles.assign'));
        $this->assertNull($user->email_verified_at);
    }

    public function test_guardian_does_not_receive_member_after_registered_event_is_replayed(): void
    {
        $this->post(route('register.store'), [
            ...$this->registrationData('guardian-repeat@example.test'),
            'account_type' => 'guardian',
        ])->assertRedirect();

        $user = User::query()->where('email', 'guardian-repeat@example.test')->firstOrFail();
        Event::dispatch(new Registered($user));
        Event::dispatch(new Registered($user));

        $this->assertSame(['guardian'], app(RbacAccessReader::class)->rolesFor($user->id));
        $this->assertSame(1, DB::table('user_roles')->where('user_id', $user->id)->count());
    }

    public function test_injected_internal_role_fields_cannot_elevate_guardian(): void
    {
        $this->post(route('register.store'), [
            ...$this->registrationData('guardian-inject@example.test'),
            'account_type' => 'guardian',
            'role' => 'admin',
            'role_code' => 'superadmin',
            'roles' => ['superadmin', 'admin'],
            'is_admin' => true,
            'email_verified_at' => now()->toDateTimeString(),
        ])->assertRedirect();

        $user = User::query()->where('email', 'guardian-inject@example.test')->firstOrFail();
        $this->assertSame(['guardian'], app(RbacAccessReader::class)->rolesFor($user->id));
        $this->assertNull($user->email_verified_at);
    }

    public function test_rejects_superadmin_as_public_account_type(): void
    {
        $this->post(route('register.store'), [
            ...$this->registrationData('forbidden-superadmin@example.test'),
            'account_type' => 'superadmin',
        ])->assertSessionHasErrors('account_type');

        $this->assertDatabaseMissing('users', ['email' => 'forbidden-superadmin@example.test']);
    }

    public function test_rejects_other_internal_roles_as_public_account_type(): void
    {
        foreach (['admin', 'editor', 'verifier'] as $role) {
            $email = 'forbidden-'.$role.'@example.test';
            $this->post(route('register.store'), [
                ...$this->registrationData($email),
                'account_type' => $role,
            ])->assertSessionHasErrors('account_type');

            $this->assertDatabaseMissing('users', ['email' => $email]);
        }
    }

    public function test_rejects_empty_account_type_instead_of_silently_defaulting(): void
    {
        $this->post(route('register.store'), [
            ...$this->registrationData('empty-account@example.test'),
            'account_type' => '',
        ])->assertSessionHasErrors('account_type');

        $this->assertDatabaseMissing('users', ['email' => 'empty-account@example.test']);
    }

    public function test_rejects_array_account_type(): void
    {
        $this->post(route('register.store'), [
            ...$this->registrationData('array-account@example.test'),
            'account_type' => ['guardian', 'superadmin'],
        ])->assertSessionHasErrors('account_type');

        $this->assertDatabaseMissing('users', ['email' => 'array-account@example.test']);
    }

    public function test_guardian_without_seeded_catalog_uses_only_minimal_guardian_fallback(): void
    {
        DB::table('role_permissions')->delete();
        DB::table('user_roles')->delete();
        DB::table('roles')->delete();

        $this->post(route('register.store'), [
            ...$this->registrationData('guardian-minimal@example.test'),
            'account_type' => 'guardian',
        ])->assertRedirect();

        $user = User::query()->where('email', 'guardian-minimal@example.test')->firstOrFail();
        $this->assertSame(['guardian'], DB::table('roles')->pluck('code')->all());
        $this->assertSame(['guardian'], app(RbacAccessReader::class)->rolesFor($user->id));
        $this->assertFalse(app(RbacAccessReader::class)->hasPermission($user->id, 'roles.assign'));
    }

    public function test_assigner_denies_internal_roles_even_when_called_directly(): void
    {
        $user = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);
        app(PublicRegistrationRoleAssigner::class)->assignToNewUser($user, 'superadmin');
    }

    public function test_guardian_cannot_open_member_dashboard(): void
    {
        $this->post(route('register.store'), [
            ...$this->registrationData('guardian-restricted@example.test'),
            'account_type' => 'guardian',
        ])->assertRedirect();

        $user = User::query()->where('email', 'guardian-restricted@example.test')->firstOrFail();
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->actingAs($user)->get('/member/dashboard')->assertForbidden();
    }

    /** @return array<string, string> */
    private function registrationData(string $email): array
    {
        return [
            'name' => 'Pendaftar MENTORA',
            'email' => $email,
            'password' => 'MentoraPassword#2026',
            'password_confirmation' => 'MentoraPassword#2026',
        ];
    }
}
