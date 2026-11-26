<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Identity;

use App\Models\User;
use Database\Seeders\RbacPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class MemberDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacPermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/member/dashboard')->assertRedirect(route('login'));
    }

    public function test_member_can_open_its_dashboard(): void
    {
        $member = $this->userWithRole('member');

        $this->actingAs($member)
            ->get('/member/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Member/Dashboard')
                ->where('member.name', $member->name)
                ->missing('member.email')
                ->etc());
    }

    public function test_an_authenticated_user_without_role_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/member/dashboard')
            ->assertForbidden();
    }

    public function test_editor_cannot_open_member_dashboard(): void
    {
        $this->actingAs($this->userWithRole('editor'))
            ->get('/member/dashboard')
            ->assertForbidden();
    }

    public function test_admin_cannot_open_member_dashboard(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get('/member/dashboard')
            ->assertForbidden();
    }

    public function test_superadmin_does_not_bypass_required_member_role(): void
    {
        $this->actingAs($this->userWithRole('superadmin'))
            ->get('/member/dashboard')
            ->assertForbidden();
    }

    public function test_unverified_member_must_verify_email(): void
    {
        $user = User::factory()->unverified()->create();
        $this->assign($user, 'member');

        $this->actingAs($user)
            ->get('/member/dashboard')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_revoked_membership_immediately_denies_access(): void
    {
        $member = $this->userWithRole('member');
        $this->actingAs($member)->get('/member/dashboard')->assertOk();

        DB::table('user_roles')
            ->where('user_id', $member->id)
            ->where('role_code', 'member')
            ->delete();

        $this->get('/member/dashboard')->assertForbidden();
    }

    public function test_the_existing_dashboard_still_works(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk();
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $this->assign($user, $role);
        return $user;
    }

    private function assign(User $user, string $role): void
    {
        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_code' => $role,
        ]);
    }
}
