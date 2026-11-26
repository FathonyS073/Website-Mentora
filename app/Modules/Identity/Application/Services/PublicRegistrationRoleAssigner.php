<?php

declare(strict_types=1);

namespace App\Modules\Identity\Application\Services;

use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Assign exactly one of the TWO public registration roles.
 *
 * Called from Fortify's CreateNewUser inside the same database transaction.
 * Administrative roles must never enter this service via request input.
 */
final class PublicRegistrationRoleAssigner
{
    private const PUBLIC_ROLES = [
        'member' => [
            'label' => 'Member',
            'description' => 'Pelajar atau mahasiswa pengguna MENTORA.',
        ],
        'guardian' => [
            'label' => 'Guardian',
            'description' => 'Orang tua atau wali yang mendampingi Member berdasarkan izin.',
        ],
    ];

    public function assignToNewUser(User $user, string $roleCode): void
    {
        if (! array_key_exists($roleCode, self::PUBLIC_ROLES)) {
            throw new InvalidArgumentException('Jenis akun publik tidak diizinkan.');
        }

        if (! $user->exists || $user->getKey() === null) {
            throw new DomainException('Akun harus tersimpan sebelum role ditetapkan.');
        }

        if (DB::table('user_roles')->where('user_id', $user->getKey())->exists()) {
            throw new DomainException('Akun sudah memiliki role. Pendaftaran tidak boleh menambah role lain.');
        }

        // Safe fallback for environments with RBAC tables but no catalog seed,
        // matching the existing S2-01E Member behavior. This never inserts an
        // internal role and does not grant permissions. Production must seed
        // RbacPermissionSeeder to enable the intended public permissions.
        $now = now();
        DB::table('roles')->insertOrIgnore([
            'code' => $roleCode,
            'label' => self::PUBLIC_ROLES[$roleCode]['label'],
            'description' => self::PUBLIC_ROLES[$roleCode]['description'],
            'is_system' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('user_roles')->insert([
            'user_id' => $user->getKey(),
            'role_code' => $roleCode,
            'assigned_at' => $now,
            'assigned_by' => null,
        ]);
    }
}
