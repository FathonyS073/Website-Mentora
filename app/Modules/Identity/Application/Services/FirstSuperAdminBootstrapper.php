<?php

declare(strict_types=1);

namespace App\Modules\Identity\Application\Services;

use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * One-time, console-only provisioning of a dedicated SuperAdmin account.
 *
 * Deliberately does not dispatch Registered: public registration has a
 * Member-role listener. The account receives no known initial password.
 */
final class FirstSuperAdminBootstrapper
{
    public function assertEligible(string $normalizedEmail): void
    {
        if (! DB::table('roles')->where('code', 'superadmin')->exists()) {
            throw new DomainException('Role superadmin belum tersedia. Jalankan seeder RBAC terlebih dahulu.');
        }

        $required = ['roles.assign', 'settings.manage'];
        $actual = DB::table('role_permissions')
            ->where('role_code', 'superadmin')
            ->whereIn('permission_code', $required)
            ->distinct()
            ->count('permission_code');

        if ($actual !== count($required)) {
            throw new DomainException('Permission SuperAdmin belum lengkap. Jalankan RbacPermissionSeeder.');
        }

        if (DB::table('user_roles')->where('role_code', 'superadmin')->exists()) {
            throw new DomainException('Bootstrap ditolak: sudah ada akun dengan role SuperAdmin.');
        }

        // Refuse to elevate an existing Member or any other existing account.
        if (User::query()->whereRaw('LOWER(email) = ?', [$normalizedEmail])->exists()) {
            throw new DomainException('Email sudah digunakan. Pilih alamat email khusus SuperAdmin yang belum terdaftar.');
        }
    }

    public function create(string $name, string $normalizedEmail): User
    {
        return DB::transaction(function () use ($name, $normalizedEmail): User {
            // The role row serializes two bootstrap attempts using this service
            // against the same PostgreSQL database.
            $role = DB::table('roles')
                ->where('code', 'superadmin')
                ->lockForUpdate()
                ->first();

            if ($role === null) {
                throw new DomainException('Role SuperAdmin tidak tersedia.');
            }

            $this->assertEligible($normalizedEmail);

            $user = User::query()->create([
                'name' => $name,
                'email' => $normalizedEmail,
                // Never print or distribute a temporary password.
                'password' => Hash::make(Str::random(64)),
                'email_verified_at' => null,
            ]);

            DB::table('user_roles')->insert([
                'user_id' => $user->getKey(),
                'role_code' => 'superadmin',
                'assigned_at' => now(),
                'assigned_by' => null,
            ]);

            return $user;
        }, 3);
    }
}
