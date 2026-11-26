<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Identity;

use App\Models\User;
use App\Modules\Identity\Application\Services\StaffInvitationService;
use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use Database\Seeders\RbacPermissionSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class StaffInvitationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacPermissionSeeder::class);
        config()->set('mail.default', 'array');
        Notification::fake();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/superadmin/users')->assertRedirect(route('login'));
        $this->post('/superadmin/staff-invitations', $this->payload())->assertRedirect(route('login'));
    }

    public function test_member_and_guardian_cannot_view_or_create_staff(): void
    {
        foreach (['member', 'guardian'] as $role) {
            $user = $this->withRole($role);
            $this->actingAs($user)->get('/superadmin/users')->assertForbidden();
            $this->post('/superadmin/staff-invitations', $this->payload())->assertForbidden();
        }

        $this->assertSame(2, User::query()->count());
    }

    public function test_admin_and_editor_and_verifier_cannot_invite_staff(): void
    {
        foreach (['admin', 'editor', 'verifier'] as $role) {
            $user = $this->withRole($role);
            $this->actingAs($user)->get('/superadmin/users')->assertForbidden();
            $this->post('/superadmin/staff-invitations', $this->payload())->assertForbidden();
        }

        $this->assertSame(0, DB::table('staff_invitation_events')->count());
    }

    public function test_unverified_superadmin_cannot_access_staff_routes(): void
    {
        $superadmin = $this->withRole('superadmin', false);

        $this->actingAs($superadmin)
            ->get('/superadmin/users')
            ->assertRedirect(route('verification.notice'));

        $this->post('/superadmin/staff-invitations', $this->payload())
            ->assertRedirect(route('verification.notice'));
    }

    public function test_superadmin_needs_roles_assign_permission_even_with_role(): void
    {
        $superadmin = $this->withRole('superadmin');
        DB::table('role_permissions')
            ->where('role_code', 'superadmin')
            ->where('permission_code', 'roles.assign')
            ->delete();

        $this->actingAs($superadmin)->get('/superadmin/users')->assertForbidden();
        $this->post('/superadmin/staff-invitations', $this->payload())->assertForbidden();
    }

    public function test_superadmin_can_open_user_listing_with_actual_roles(): void
    {
        $superadmin = $this->withRole('superadmin');
        $member = $this->withRole('member');

        $this->actingAs($superadmin)
            ->get('/superadmin/users')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/SuperAdmin/Users')
                ->where('users.total', 2)
                ->where('administrator.name', $superadmin->name)
                ->has('users.data', 2)
                ->etc());

        $this->assertSame(['member'], app(RbacAccessReader::class)->rolesFor($member->id));
    }

    public function test_superadmin_invites_editor_with_audited_assignment_and_reset_notification(): void
    {
        $superadmin = $this->withRole('superadmin');

        $this->actingAs($superadmin)
            ->post('/superadmin/staff-invitations', $this->payload())
            ->assertRedirect(route('superadmin.users.index'));

        $editor = User::query()->where('email', 'editor-new@example.test')->firstOrFail();
        $this->assertNull($editor->email_verified_at);
        $this->assertNotSame('', (string) $editor->password);
        $this->assertSame(['editor'], app(RbacAccessReader::class)->rolesFor($editor->id));
        $this->assertFalse(app(RbacAccessReader::class)->hasRole($editor->id, 'member'));
        $this->assertFalse(app(RbacAccessReader::class)->hasPermission($editor->id, 'roles.assign'));
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $editor->id,
            'role_code' => 'editor',
            'assigned_by' => $superadmin->id,
        ]);
        $this->assertDatabaseHas('staff_invitation_events', [
            'actor_user_id' => $superadmin->id,
            'target_user_id' => $editor->id,
            'role_code' => 'editor',
            'event' => 'created',
        ]);
        $this->assertDatabaseHas('staff_invitation_events', [
            'target_user_id' => $editor->id,
            'event' => 'mail_sent',
        ]);
        Notification::assertSentTo($editor, ResetPassword::class);
    }

    public function test_superadmin_can_invite_verifier_or_admin_without_superadmin_role(): void
    {
        $superadmin = $this->withRole('superadmin');

        foreach (['verifier', 'admin'] as $role) {
            $email = $role.'-new@example.test';
            $this->actingAs($superadmin)
                ->post('/superadmin/staff-invitations', [
                    'name' => 'Akun Internal', 'email' => $email, 'role_code' => $role,
                ])->assertRedirect(route('superadmin.users.index'));

            $invited = User::query()->where('email', $email)->firstOrFail();
            $this->assertSame([$role], app(RbacAccessReader::class)->rolesFor($invited->id));
            $this->assertFalse(app(RbacAccessReader::class)->hasPermission($invited->id, 'roles.assign'));
        }

        $this->assertSame(0, DB::table('user_roles')->where('role_code', 'superadmin')
            ->where('user_id', '!=', $superadmin->id)->count());
    }

    public function test_public_or_privileged_roles_cannot_be_invited(): void
    {
        $superadmin = $this->withRole('superadmin');

        foreach (['superadmin', 'member', 'guardian', 'unknown'] as $role) {
            $email = $role.'-invite@example.test';
            $this->actingAs($superadmin)
                ->post('/superadmin/staff-invitations', [
                    ...$this->payload(), 'email' => $email, 'role_code' => $role,
                ])
                ->assertSessionHasErrors('role_code');
            $this->assertDatabaseMissing('users', ['email' => $email]);
        }
    }

    public function test_existing_account_cannot_be_promoted_even_if_email_case_differs(): void
    {
        $superadmin = $this->withRole('superadmin');
        $member = $this->withRole('member');
        $member->forceFill(['email' => 'existing@example.test'])->save();

        $this->actingAs($superadmin)
            ->post('/superadmin/staff-invitations', [
                ...$this->payload(), 'email' => 'EXISTING@EXAMPLE.TEST', 'role_code' => 'admin',
            ])->assertSessionHasErrors('email');

        $this->assertSame(['member'], app(RbacAccessReader::class)->rolesFor($member->id));
        $this->assertSame(0, DB::table('staff_invitation_events')->count());
    }

    public function test_missing_and_invalid_fields_are_rejected_without_creating_account(): void
    {
        $superadmin = $this->withRole('superadmin');

        $this->actingAs($superadmin)
            ->post('/superadmin/staff-invitations', [
                'name' => 'A', 'email' => 'not-email', 'role_code' => ['editor'],
            ])->assertSessionHasErrors(['name', 'email', 'role_code']);

        $this->assertSame(1, User::query()->count());
    }

    public function test_application_service_refuses_non_superadmin_direct_calls(): void
    {
        $member = $this->withRole('member');
        $this->expectException(AuthorizationException::class);

        app(StaffInvitationService::class)->invite(
            $member,
            'Internal User',
            'internal@example.test',
            'admin',
        );
    }

    public function test_email_failure_is_audited_and_invited_account_is_not_rolled_back(): void
    {
        $superadmin = $this->withRole('superadmin');
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::INVALID_USER);

        $this->actingAs($superadmin)
            ->post('/superadmin/staff-invitations', $this->payload())
            ->assertRedirect(route('superadmin.users.index'));

        $editor = User::query()->where('email', 'editor-new@example.test')->firstOrFail();
        $this->assertSame(['editor'], app(RbacAccessReader::class)->rolesFor($editor->id));
        $this->assertDatabaseHas('staff_invitation_events', [
            'target_user_id' => $editor->id,
            'event' => 'mail_failed',
        ]);
    }

    public function test_can_resend_activation_after_cooldown_without_reassigning_role(): void
    {
        $superadmin = $this->withRole('superadmin');
        $this->actingAs($superadmin)
            ->post('/superadmin/staff-invitations', $this->payload())
            ->assertRedirect();

        $editor = User::query()->where('email', 'editor-new@example.test')->firstOrFail();

        $this->post("/superadmin/staff-invitations/{$editor->id}/resend")
            ->assertRedirect(route('superadmin.users.index'));
        $this->assertDatabaseMissing('staff_invitation_events', ['target_user_id' => $editor->id, 'event' => 'resent']);

        $this->travel(61)->seconds();
        $this->post("/superadmin/staff-invitations/{$editor->id}/resend")
            ->assertRedirect(route('superadmin.users.index'));

        $this->assertDatabaseHas('staff_invitation_events', ['target_user_id' => $editor->id, 'event' => 'resent']);
        $this->assertSame(1, DB::table('user_roles')->where('user_id', $editor->id)->count());
        Notification::assertSentToTimes($editor, ResetPassword::class, 2);
    }

    public function test_resend_refuses_regular_user_and_verified_invited_staff(): void
    {
        $superadmin = $this->withRole('superadmin');
        $member = $this->withRole('member');
        $this->actingAs($superadmin)
            ->post("/superadmin/staff-invitations/{$member->id}/resend")
            ->assertRedirect(route('superadmin.users.index'));

        $this->post('/superadmin/staff-invitations', $this->payload())->assertRedirect();
        $editor = User::query()->where('email', 'editor-new@example.test')->firstOrFail();
        $editor->forceFill(['email_verified_at' => now()])->save();
        $this->post("/superadmin/staff-invitations/{$editor->id}/resend")
            ->assertRedirect(route('superadmin.users.index'));

        $this->assertSame(0, DB::table('staff_invitation_events')
            ->whereIn('event', ['resent', 'resend_failed'])->count());
    }

    public function test_resend_refuses_invitation_if_privileged_role_was_later_added(): void
    {
        $superadmin = $this->withRole('superadmin');
        $this->actingAs($superadmin)
            ->post('/superadmin/staff-invitations', $this->payload())->assertRedirect();
        $editor = User::query()->where('email', 'editor-new@example.test')->firstOrFail();

        DB::table('user_roles')->insert([
            'user_id' => $editor->id,
            'role_code' => 'superadmin',
        ]);

        $this->travel(61)->seconds();
        $this->post("/superadmin/staff-invitations/{$editor->id}/resend")
            ->assertRedirect(route('superadmin.users.index'));

        $this->assertSame(0, DB::table('staff_invitation_events')
            ->where('event', 'resent')->count());
    }

    public function test_revoked_superadmin_cannot_send_further_invitations(): void
    {
        $superadmin = $this->withRole('superadmin');
        $this->actingAs($superadmin)->get('/superadmin/users')->assertOk();

        DB::table('user_roles')->where('user_id', $superadmin->id)->delete();

        $this->post('/superadmin/staff-invitations', $this->payload())->assertForbidden();
        $this->get('/superadmin/users')->assertForbidden();
    }

    private function withRole(string $role, bool $verified = true): User
    {
        $user = $verified ? User::factory()->create() : User::factory()->unverified()->create();
        DB::table('user_roles')->insert(['user_id' => $user->getKey(), 'role_code' => $role]);

        return $user;
    }

    /** @return array{name: string, email: string, role_code: string} */
    private function payload(): array
    {
        return [
            'name' => 'Editor MENTORA',
            'email' => 'editor-new@example.test',
            'role_code' => 'editor',
        ];
    }
}
