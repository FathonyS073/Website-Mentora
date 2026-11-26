<?php

declare(strict_types=1);

namespace App\Modules\Identity\Application\Services;

use App\Models\User;
use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Throwable;

/**
 * S2-01F.3: invite NEW internal staff accounts, never elevate existing users.
 *
 * Only a verified SuperAdmin with roles.assign may invoke this service.
 * An invite creates an unusable random password and delivers a standard
 * Laravel password-reset notification; no plaintext password/token is stored.
 * Role grants use the seeded RBAC catalog. Existing accounts are immutable here.
 */
final readonly class StaffInvitationService
{
    /** @var list<string> */
    public const ALLOWED_ROLES = ['editor', 'verifier', 'admin'];

    public function __construct(private RbacAccessReader $access) {}

    /**
     * @return array{user: User, delivered: bool}
     * @throws AuthorizationException|DomainException
     */
    public function invite(User $actor, string $name, string $email, string $roleCode): array
    {
        $this->assertSuperAdmin($actor);
        $name = trim($name);
        $email = Str::lower(trim($email));
        $this->assertInput($name, $email, $roleCode);

        // A row-level lock serializes two invitations and protects the
        // application-level case-insensitive uniqueness check on PostgreSQL.
        $user = DB::transaction(function () use ($actor, $name, $email, $roleCode): User {
            $lock = DB::table('roles')->where('code', 'superadmin')->lockForUpdate()->first();

            if ($lock === null) {
                throw new DomainException('Katalog RBAC belum siap.');
            }

            // Re-check authorization at the actual write boundary.
            $this->assertSuperAdmin($actor);

            if (User::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
                throw new DomainException('Email sudah digunakan. Akun yang ada tidak boleh dinaikkan role melalui undangan.');
            }

            if (
                ! DB::table('roles')->where('code', $roleCode)->exists()
                || ! DB::table('role_permissions')->where('role_code', $roleCode)->exists()
            ) {
                throw new DomainException('Role atau permission belum tersedia. Periksa seeder RBAC.');
            }

            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(64)),
                'email_verified_at' => null,
            ]);

            DB::table('user_roles')->insert([
                'user_id' => $user->getKey(),
                'role_code' => $roleCode,
                'assigned_at' => now(),
                'assigned_by' => $actor->getKey(),
            ]);

            $this->audit($actor, $user, $roleCode, 'created');

            return $user;
        }, 3);

        return ['user' => $user, 'delivered' => $this->sendActivation($actor, $user, $roleCode, false)];
    }

    /**
     * Resend activation only to an existing, unverified account created
     * through this dedicated invitation flow; never reassigns a role.
     *
     * @return bool Whether the password setup notification was sent/queued.
     * @throws AuthorizationException|DomainException
     */
    public function resend(User $actor, User $user): bool
    {
        $this->assertSuperAdmin($actor);

        $invitation = DB::table('staff_invitation_events')
            ->where('target_user_id', $user->getKey())
            ->where('event', 'created')
            ->orderByDesc('id')
            ->first();

        if ($invitation === null || ! in_array($invitation->role_code, self::ALLOWED_ROLES, true)) {
            throw new DomainException('Akun ini bukan akun staf yang diundang melalui MENTORA.');
        }

        if ($user->hasVerifiedEmail()) {
            throw new DomainException('Email akun sudah terverifikasi. Tidak perlu mengirim ulang undangan.');
        }

        // Block resends if a successful invitation email was just requested.
        if (DB::table('staff_invitation_events')
            ->where('target_user_id', $user->getKey())
            ->whereIn('event', ['mail_sent', 'resent'])
            ->where('created_at', '>', now()->subMinute())
            ->exists()) {
            throw new DomainException('Tunggu sekurang-kurangnya 60 detik sebelum mengirim ulang.');
        }

        // Do not send new access links if the invited account has since
        // acquired another role (particularly a privileged one).
        $currentRoles = DB::table('user_roles')
            ->where('user_id', $user->getKey())
            ->pluck('role_code')
            ->all();

        if (count($currentRoles) !== 1 || $currentRoles[0] !== $invitation->role_code) {
            throw new DomainException('Role undangan sudah berubah. Hubungi pengelola keamanan sistem.');
        }

        return $this->sendActivation($actor, $user, $invitation->role_code, true);
    }

    /** @throws AuthorizationException */
    private function assertSuperAdmin(User $actor): void
    {
        $id = $actor->getAuthIdentifier();

        if (
            (! is_int($id) && ! is_string($id))
            || ! $actor->hasVerifiedEmail()
            || ! $this->access->hasRole($id, 'superadmin')
            || ! $this->access->hasPermission($id, 'roles.assign')
        ) {
            throw new AuthorizationException('Akses undangan staf hanya untuk SuperAdmin terverifikasi.');
        }
    }

    /** @throws DomainException */
    private function assertInput(string $name, string $email, string $roleCode): void
    {
        if (Str::length($name) < 3 || Str::length($name) > 255) {
            throw new DomainException('Nama harus memiliki 3–255 karakter.');
        }

        if (strlen($email) > 254 || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Alamat email tidak valid.');
        }

        if (! in_array($roleCode, self::ALLOWED_ROLES, true)) {
            throw new DomainException('Hanya role Editor, Verifier, atau Admin yang boleh diundang.');
        }
    }

    private function sendActivation(User $actor, User $user, string $roleCode, bool $resend): bool
    {
        // Notification delivery is intentionally AFTER account transaction.
        // A mail failure does not roll back the account; SuperAdmin may retry.
        try {
            $status = Password::sendResetLink(['email' => $user->email]);
            $ok = $status === Password::RESET_LINK_SENT;
        } catch (Throwable $exception) {
            report($exception);
            $ok = false;
        }

        $event = $ok ? ($resend ? 'resent' : 'mail_sent') : ($resend ? 'resend_failed' : 'mail_failed');
        $this->audit($actor, $user, $roleCode, $event);

        return $ok;
    }

    private function audit(User $actor, User $user, string $roleCode, string $event): void
    {
        DB::table('staff_invitation_events')->insert([
            'actor_user_id' => $actor->getKey(),
            'target_user_id' => $user->getKey(),
            'role_code' => $roleCode,
            'event' => $event,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
