<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Identity;

use App\Models\User;
use Database\Seeders\RbacPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** Tests authorization in real HTTP requests without creating production routes. */
final class RbacMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacPermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login_on_role_route(): void
    {
        $url = $this->probe('rbac.role:member');
        $this->get($url)->assertRedirect(route('login'));
    }

    public function test_guest_gets_unauthorized_json_on_permission_route(): void
    {
        $url = $this->probe('rbac.permission:profile.view');
        $this->getJson($url)->assertUnauthorized();
    }

    public function test_authenticated_user_without_role_receives_forbidden(): void
    {
        $url = $this->probe('rbac.role:member');
        $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
    }

    public function test_member_can_access_member_role_but_not_admin_role(): void
    {
        $member = $this->userWithRole('member');
        $memberUrl = $this->probe('rbac.role:member');
        $adminUrl = $this->probe('rbac.role:admin');

        $this->actingAs($member)->get($memberUrl)->assertOk();
        $this->get($adminUrl)->assertForbidden();
    }

    public function test_unassigned_user_has_no_profile_permission(): void
    {
        $url = $this->probe('rbac.permission:profile.view');
        $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
    }

    public function test_member_gets_profile_view_but_cannot_publish(): void
    {
        $member = $this->userWithRole('member');
        $viewUrl = $this->probe('rbac.permission:profile.view');
        $publishUrl = $this->probe('rbac.permission:content.publish');

        $this->actingAs($member)->get($viewUrl)->assertOk();
        $this->get($publishUrl)->assertForbidden();
    }

    public function test_editor_and_verifier_permissions_are_separated(): void
    {
        $draftUrl = $this->probe('rbac.permission:content.article.draft');
        $reviewUrl = $this->probe('rbac.permission:content.review');
        $editor = $this->userWithRole('editor');
        $verifier = $this->userWithRole('verifier');

        $this->actingAs($editor)->get($draftUrl)->assertOk();
        $this->get($reviewUrl)->assertForbidden();
        $this->actingAs($verifier)->get($reviewUrl)->assertOk();
        $this->get($draftUrl)->assertForbidden();
    }

    public function test_admin_cannot_assign_roles_or_change_settings(): void
    {
        $admin = $this->userWithRole('admin');
        $manageUsers = $this->probe('rbac.permission:users.manage');
        $assignRoles = $this->probe('rbac.permission:roles.assign');
        $manageSettings = $this->probe('rbac.permission:settings.manage');

        $this->actingAs($admin)->get($manageUsers)->assertOk();
        $this->get($assignRoles)->assertForbidden();
        $this->get($manageSettings)->assertForbidden();
    }

    public function test_superadmin_works_only_after_explicit_assignment(): void
    {
        $user = User::factory()->create();
        $url = $this->probe('rbac.permission:roles.assign');

        $this->actingAs($user)->get($url)->assertForbidden();
        $this->assign($user, 'superadmin');
        $this->get($url)->assertOk();
    }

    public function test_removing_role_revokes_permission_without_new_login(): void
    {
        $member = $this->userWithRole('member');
        $url = $this->probe('rbac.permission:tracker.manage');
        $this->actingAs($member)->get($url)->assertOk();

        DB::table('user_roles')->where('user_id', $member->id)->delete();
        $this->get($url)->assertForbidden();
    }

    public function test_unknown_or_missing_codes_fail_closed(): void
    {
        $superadmin = $this->userWithRole('superadmin');
        $this->actingAs($superadmin);

        $this->get($this->probe('rbac.role:does-not-exist'))->assertForbidden();
        $this->get($this->probe('rbac.permission:unknown.permission'))->assertForbidden();
        $this->get($this->probe('rbac.role'))->assertForbidden();
        $this->get($this->probe('rbac.permission'))->assertForbidden();
    }

    public function test_gates_apply_same_permission_matrix(): void
    {
        $member = $this->userWithRole('member');
        $admin = $this->userWithRole('admin');

        $this->assertTrue(Gate::forUser($member)->allows('tracker.manage'));
        $this->assertFalse(Gate::forUser($member)->allows('roles.assign'));
        $this->assertTrue(Gate::forUser($admin)->allows('users.manage'));
        $this->assertFalse(Gate::forUser($admin)->allows('settings.manage'));
        $this->assertFalse(Gate::forUser($admin)->allows('permission.unknown'));
    }

    public function test_laravel_can_middleware_enforces_registered_gates(): void
    {
        $url = $this->probe('can:profile.view');
        $guest = User::factory()->create();
        $member = $this->userWithRole('member');

        $this->actingAs($guest)->get($url)->assertForbidden();
        $this->actingAs($member)->get($url)->assertOk();
    }

    public function test_current_public_and_authenticated_dashboard_routes_remain_unchanged(): void
    {
        $this->get('/')->assertOk();
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertOk();
    }

    private function probe(string $middleware): string
    {
        $uri = '/__rbac-test/route-'.uniqid('', false);

        Route::middleware(['web', $middleware])
            ->get($uri, static fn () => response('authorized'));

        return $uri;
    }

    private function userWithRole(string $roleCode): User
    {
        $user = User::factory()->create();
        $this->assign($user, $roleCode);

        return $user;
    }

    private function assign(User $user, string $roleCode): void
    {
        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_code' => $roleCode,
        ]);
    }
}
