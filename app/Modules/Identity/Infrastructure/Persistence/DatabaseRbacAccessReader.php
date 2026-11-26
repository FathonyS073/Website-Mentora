<?php

declare(strict_types=1);

namespace App\Modules\Identity\Infrastructure\Persistence;

use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use Illuminate\Support\Facades\DB;

/**
 * Default-deny DB-backed RBAC lookups.
 * In particular, 'superadmin' is not a magic bypass: grants must exist in
 * role_permissions, and the user must explicitly own that role.
 */
final class DatabaseRbacAccessReader implements RbacAccessReader
{
    public function rolesFor(int|string $userId): array
    {
        return DB::table('user_roles')
            ->where('user_id', $userId)
            ->orderBy('role_code')
            ->pluck('role_code')
            ->all();
    }

    public function permissionsFor(int|string $userId): array
    {
        return DB::table('user_roles as ur')
            ->join('role_permissions as rp', 'rp.role_code', '=', 'ur.role_code')
            ->where('ur.user_id', $userId)
            ->distinct()
            ->orderBy('rp.permission_code')
            ->pluck('rp.permission_code')
            ->all();
    }

    public function hasRole(int|string $userId, string $roleCode): bool
    {
        if ($roleCode === '') {
            return false;
        }

        return DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('role_code', $roleCode)
            ->exists();
    }

    public function hasPermission(int|string $userId, string $permissionCode): bool
    {
        if ($permissionCode === '') {
            return false;
        }

        return DB::table('user_roles as ur')
            ->join('role_permissions as rp', 'rp.role_code', '=', 'ur.role_code')
            ->where('ur.user_id', $userId)
            ->where('rp.permission_code', $permissionCode)
            ->exists();
    }
}
