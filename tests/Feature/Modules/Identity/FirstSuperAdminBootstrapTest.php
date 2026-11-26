<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Identity;

use App\Models\User;
use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use Database\Seeders\RbacPermissionSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

final class FirstSuperAdminBootstrapTest extends TestCase
{
    use RefreshDatabase;

    private const EMAIL = 'root-mentora@example.test';
    private const NAME = 'Pemilik MENTORA';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacPermissionSeeder::class);
        config()->set('mail.default', 'array');
    }

    public function test_command_is_available(): void
    {
        $this->assertTrue(
            array_key_exists('mentora:rbac:bootstrap-superadmin', \Illuminate\Support\Facades\Artisan::all())
        );
    }

    public function test_default_mode_is_preview_and_never_changes_database(): void
    {
        $this->artisan('mentora:rbac:bootstrap-superadmin', $this->bootstrapOptions())
            ->assertExitCode(0);

        $this->assertSame(0, User::query()->count());
        $this->assertSame(0, DB::table('user_roles')->count());
    }

    public function test_apply_requires_exact_email_confirmation(): void
    {
        $this->artisan('mentora:rbac:bootstrap-superadmin', [
            ...$this->bootstrapOptions(), '--apply' => true, '--confirm' => 'different@example.test',
        ])->assertExitCode(1);

        $this->assertSame(0, User::query()->count());
    }

    public function test_invalid_email_or_name_is_rejected(): void
    {
        $this->artisan('mentora:rbac:bootstrap-superadmin', [
            '--name' => 'Ab', '--email' => self::EMAIL,
        ])->assertExitCode(1);

        $this->artisan('mentora:rbac:bootstrap-superadmin', [
            '--name' => self::NAME, '--email' => 'not-an-email',
        ])->assertExitCode(1);

        $this->assertSame(0, User::query()->count());
    }

    public function test_creates_one_dedicated_account_without_a_known_password(): void
    {
        Notification::fake();

        $this->artisan('mentora:rbac:bootstrap-superadmin', $this->applyOptions())
            ->assertExitCode(0);

        $user = User::query()->where('email', self::EMAIL)->firstOrFail();
        $reader = app(RbacAccessReader::class);

        $this->assertNull($user->email_verified_at);
        $this->assertNotSame('', (string) $user->password);
        $this->assertSame(['superadmin'], $reader->rolesFor($user->id));
        $this->assertTrue($reader->hasPermission($user->id, 'roles.assign'));
        $this->assertTrue($reader->hasPermission($user->id, 'settings.manage'));
        $this->assertFalse($reader->hasRole($user->id, 'member'));
        $this->assertSame(1, DB::table('user_roles')->count());

        Notification::assertSentTo($user, ResetPassword::class);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_second_bootstrap_is_refused_without_touching_existing_grant(): void
    {
        Notification::fake();
        $this->artisan('mentora:rbac:bootstrap-superadmin', $this->applyOptions())
            ->assertExitCode(0);

        $this->artisan('mentora:rbac:bootstrap-superadmin', [
            '--name' => 'Other Root', '--email' => 'other-root@example.test',
            '--apply' => true, '--confirm' => 'other-root@example.test',
        ])->assertExitCode(1);

        $this->assertSame(1, DB::table('user_roles')->where('role_code', 'superadmin')->count());
        $this->assertSame(1, User::query()->count());
    }

    public function test_existing_member_cannot_be_silently_promoted(): void
    {
        $user = User::factory()->create(['email' => self::EMAIL]);
        DB::table('user_roles')->insert([
            'user_id' => $user->id, 'role_code' => 'member',
        ]);

        $this->artisan('mentora:rbac:bootstrap-superadmin', $this->applyOptions())
            ->assertExitCode(1);

        $this->assertSame(['member'], app(RbacAccessReader::class)->rolesFor($user->id));
        $this->assertSame(1, User::query()->count());
    }

    public function test_missing_role_catalog_prevents_bootstrap(): void
    {
        DB::table('role_permissions')->where('role_code', 'superadmin')->delete();
        DB::table('roles')->where('code', 'superadmin')->delete();

        $this->artisan('mentora:rbac:bootstrap-superadmin', $this->applyOptions())
            ->assertExitCode(1);

        $this->assertSame(0, User::query()->count());
    }

    public function test_missing_critical_permission_prevents_bootstrap(): void
    {
        DB::table('role_permissions')
            ->where('role_code', 'superadmin')
            ->where('permission_code', 'roles.assign')
            ->delete();

        $this->artisan('mentora:rbac:bootstrap-superadmin', $this->applyOptions())
            ->assertExitCode(1);

        $this->assertSame(0, User::query()->count());
    }

    public function test_bootstrap_does_not_dispatch_public_registration_event(): void
    {
        Notification::fake();
        Event::fake([Registered::class]);

        $this->artisan('mentora:rbac:bootstrap-superadmin', $this->applyOptions())
            ->assertExitCode(0);

        Event::assertNotDispatched(Registered::class);
        $this->assertSame(0, DB::table('user_roles')->where('role_code', 'member')->count());
    }

    /** @return array<string, string> */
    private function bootstrapOptions(): array
    {
        return ['--name' => self::NAME, '--email' => self::EMAIL];
    }

    /** @return array<string, mixed> */
    private function applyOptions(): array
    {
        return [
            ...$this->bootstrapOptions(),
            '--apply' => true,
            '--confirm' => self::EMAIL,
        ];
    }
}
