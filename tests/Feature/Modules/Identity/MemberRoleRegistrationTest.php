<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Identity;

use App\Models\User;
use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use Database\Seeders\RbacPermissionSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class MemberRoleRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacPermissionSeeder::class);
    }

    public function test_public_registration_grants_only_member(): void
    {
        $this->post('/register', $this->registrationData('registrasi-1@example.test'))
            ->assertRedirect();

        $user = User::query()->where('email', 'registrasi-1@example.test')->firstOrFail();
        $access = app(RbacAccessReader::class);

        $this->assertSame(['member'], $access->rolesFor($user->id));
        $this->assertTrue($access->hasPermission($user->id, 'tracker.manage'));
        $this->assertFalse($access->hasPermission($user->id, 'roles.assign'));
        $this->assertFalse($access->hasRole($user->id, 'admin'));
        $this->assertFalse($access->hasRole($user->id, 'superadmin'));
    }

    public function test_public_registration_ignores_administrative_role_fields(): void
    {
        $this->post('/register', [
            ...$this->registrationData('registrasi-2@example.test'),
            'role' => 'superadmin',
            'role_code' => 'admin',
            'roles' => ['superadmin'],
            'is_admin' => true,
        ])->assertRedirect();

        $user = User::query()->where('email', 'registrasi-2@example.test')->firstOrFail();
        $this->assertSame(['member'], app(RbacAccessReader::class)->rolesFor($user->id));
    }

    public function test_factory_user_still_has_no_implicit_role(): void
    {
        $user = User::factory()->create();
        $this->assertSame([], app(RbacAccessReader::class)->rolesFor($user->id));
    }

    public function test_repeated_registered_event_does_not_duplicate_member(): void
    {
        $user = User::factory()->create();

        Event::dispatch(new Registered($user));
        Event::dispatch(new Registered($user));

        $this->assertSame(1, DB::table('user_roles')->where('user_id', $user->id)->count());
        $this->assertSame(['member'], app(RbacAccessReader::class)->rolesFor($user->id));
    }

    public function test_registered_event_does_not_modify_existing_privileged_role(): void
    {
        $user = User::factory()->create();
        DB::table('user_roles')->insert(['user_id' => $user->id, 'role_code' => 'admin']);

        Event::dispatch(new Registered($user));

        $this->assertSame(['admin'], app(RbacAccessReader::class)->rolesFor($user->id));
    }

    public function test_minimal_member_catalog_fallback_does_not_create_privileged_roles(): void
    {
        DB::table('role_permissions')->delete();
        DB::table('user_roles')->delete();
        DB::table('roles')->delete();
        $user = User::factory()->create();

        Event::dispatch(new Registered($user));

        $this->assertSame(['member'], DB::table('roles')->pluck('code')->all());
        $this->assertSame(['member'], app(RbacAccessReader::class)->rolesFor($user->id));
        $this->assertFalse(app(RbacAccessReader::class)->hasPermission($user->id, 'roles.assign'));
    }

    public function test_backfill_is_preview_only_by_default(): void
    {
        $user = User::factory()->create();

        $this->artisan('mentora:rbac:backfill-members --all')
            ->assertExitCode(0);

        $this->assertSame(0, DB::table('user_roles')->where('user_id', $user->id)->count());
    }

    public function test_backfill_requires_explicit_scope(): void
    {
        $user = User::factory()->create();

        $this->artisan('mentora:rbac:backfill-members --apply')
            ->assertExitCode(1);

        $this->assertSame(0, DB::table('user_roles')->where('user_id', $user->id)->count());
    }

    public function test_backfill_assigns_only_unassigned_users_and_is_repeatable(): void
    {
        $unassigned = User::factory()->create();
        $admin = User::factory()->create();
        DB::table('user_roles')->insert(['user_id' => $admin->id, 'role_code' => 'admin']);

        $this->artisan('mentora:rbac:backfill-members --all --apply')
            ->assertExitCode(0);
        $this->artisan('mentora:rbac:backfill-members --all --apply')
            ->assertExitCode(0);

        $this->assertSame(['member'], app(RbacAccessReader::class)->rolesFor($unassigned->id));
        $this->assertSame(['admin'], app(RbacAccessReader::class)->rolesFor($admin->id));
        $this->assertSame(2, DB::table('user_roles')->count());
    }

    public function test_targeted_backfill_does_not_touch_other_accounts(): void
    {
        $selected = User::factory()->create();
        $other = User::factory()->create();

        $this->artisan('mentora:rbac:backfill-members --user-id='.$selected->id.' --apply')
            ->assertExitCode(0);

        $this->assertSame(['member'], app(RbacAccessReader::class)->rolesFor($selected->id));
        $this->assertSame([], app(RbacAccessReader::class)->rolesFor($other->id));
    }

    /** @return array<string, string> */
    private function registrationData(string $email): array
    {
        return [
            'name' => 'Member Baru',
            'email' => $email,
            'password' => 'MentoraPassword#2026',
            'password_confirmation' => 'MentoraPassword#2026',
        ];
    }
}
