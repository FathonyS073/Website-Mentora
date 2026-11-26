<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Identity;

use App\Models\User;
use App\Modules\Identity\Application\Queries\GetUserAccess;
use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use App\Modules\Identity\Domain\Permissions\RbacPermissionMatrix;
use App\Modules\Identity\Infrastructure\Models\Permission;
use App\Modules\Identity\Infrastructure\Models\Role;
use App\Modules\Identity\Infrastructure\Persistence\DatabaseRbacAccessReader;
use Database\Seeders\RbacPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class RbacAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacPermissionSeeder::class);
    }

    public function test_repository_binding_resolves_without_changing_identity_provider(): void
    {
        $this->assertInstanceOf(
            DatabaseRbacAccessReader::class,
            app(RbacAccessReader::class),
        );
    }

    public function test_permission_catalog_has_eighteen_codes_and_six_role_mappings(): void
    {
        $this->assertCount(18, RbacPermissionMatrix::permissions());
        $this->assertCount(6, RbacPermissionMatrix::grants());
        $this->assertEqualsCanonicalizing(
            array_keys(RbacPermissionMatrix::grants()),
            DB::table('roles')->pluck('code')->all(),
        );
        $this->assertSame(18, DB::table('permissions')->count());
    }

    public function test_seed_is_repeatable_and_synchronizes_permission_grants(): void
    {
        $before = DB::table('role_permissions')->count();
        $this->seed(RbacPermissionSeeder::class);
        $this->assertSame($before, DB::table('role_permissions')->count());
        $this->assertSame(0, DB::table('user_roles')->count());
        $this->assertSame(42, $before);
    }

    public function test_new_user_has_no_implicit_role_or_permission(): void
    {
        $user = User::factory()->create();
        $reader = app(RbacAccessReader::class);

        $this->assertSame([], $reader->rolesFor($user->id));
        $this->assertSame([], $reader->permissionsFor($user->id));
        $this->assertFalse($reader->hasRole($user->id, 'member'));
        $this->assertFalse($reader->hasPermission($user->id, 'profile.view'));
        $this->assertFalse($reader->hasPermission($user->id, 'roles.assign'));
    }

    public function test_member_can_only_access_member_permissions(): void
    {
        $user = $this->assign('member');
        $reader = app(RbacAccessReader::class);

        $this->assertTrue($reader->hasRole($user->id, 'member'));
        $this->assertTrue($reader->hasPermission($user->id, 'scholarship.save'));
        $this->assertTrue($reader->hasPermission($user->id, 'tracker.manage'));
        $this->assertFalse($reader->hasPermission($user->id, 'content.publish'));
        $this->assertFalse($reader->hasPermission($user->id, 'roles.assign'));
        $this->assertFalse($reader->hasPermission($user->id, 'nonexistent.permission'));
    }

    public function test_editor_cannot_review_or_publish_and_verifier_cannot_edit_drafts(): void
    {
        $editor = $this->assign('editor');
        $verifier = $this->assign('verifier');
        $reader = app(RbacAccessReader::class);

        $this->assertTrue($reader->hasPermission($editor->id, 'content.article.draft'));
        $this->assertFalse($reader->hasPermission($editor->id, 'content.review'));
        $this->assertFalse($reader->hasPermission($editor->id, 'content.publish'));
        $this->assertTrue($reader->hasPermission($verifier->id, 'content.review'));
        $this->assertFalse($reader->hasPermission($verifier->id, 'content.article.draft'));
        $this->assertFalse($reader->hasPermission($verifier->id, 'content.publish'));
    }

    public function test_admin_cannot_assign_roles_or_change_system_settings(): void
    {
        $admin = $this->assign('admin');
        $reader = app(RbacAccessReader::class);

        $this->assertTrue($reader->hasPermission($admin->id, 'users.manage'));
        $this->assertTrue($reader->hasPermission($admin->id, 'content.publish'));
        $this->assertFalse($reader->hasPermission($admin->id, 'roles.assign'));
        $this->assertFalse($reader->hasPermission($admin->id, 'settings.manage'));
    }

    public function test_superadmin_privileges_require_explicit_assignment(): void
    {
        $user = User::factory()->create();
        $reader = app(RbacAccessReader::class);

        $this->assertFalse($reader->hasPermission($user->id, 'roles.assign'));

        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_code' => 'superadmin',
        ]);

        $this->assertTrue($reader->hasPermission($user->id, 'roles.assign'));
        $this->assertTrue($reader->hasPermission($user->id, 'settings.manage'));
        $this->assertCount(18, $reader->permissionsFor($user->id));
    }

    public function test_multiple_roles_produce_unique_union_and_application_snapshot(): void
    {
        $user = $this->assign('member');
        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_code' => 'editor',
        ]);

        $snapshot = app(GetUserAccess::class)->execute($user->id);

        $this->assertSame(['editor', 'member'], $snapshot->roles);
        $this->assertTrue($snapshot->hasRole('editor'));
        $this->assertTrue($snapshot->can('scholarship.save'));
        $this->assertTrue($snapshot->can('content.article.draft'));
        $this->assertFalse($snapshot->can('content.publish'));
        $this->assertCount(8, $snapshot->permissions);
    }

    public function test_role_and_permission_models_use_string_keys_and_relationships(): void
    {
        $role = Role::query()->findOrFail('member');
        $permission = Permission::query()->findOrFail('profile.view');

        $this->assertSame('member', $role->getKey());
        $this->assertSame('profile.view', $permission->getKey());
        $this->assertFalse($role->getIncrementing());
        $this->assertFalse($permission->getIncrementing());
        $this->assertSame(5, $role->permissions()->count());
        $this->assertSame(6, $permission->roles()->count());
    }

    public function test_guardian_permission_does_not_grant_access_to_other_users_data(): void
    {
        $guardian = $this->assign('guardian');
        $reader = app(RbacAccessReader::class);

        // This is only a role-level entitlement, NOT proof of member consent.
        // S2-01D must check guardian/member relationship on each resource.
        $this->assertTrue($reader->hasPermission($guardian->id, 'guardian.progress.view'));
        $this->assertFalse($reader->hasPermission($guardian->id, 'roles.assign'));
        $this->assertFalse($reader->hasPermission($guardian->id, 'tracker.manage'));
    }

    private function assign(string $roleCode): User
    {
        $user = User::factory()->create();
        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_code' => $roleCode,
        ]);

        return $user;
    }
}
