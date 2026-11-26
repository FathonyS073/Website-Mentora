<?php

declare(strict_types=1);

namespace App\Modules\Identity\Application\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Assigns the only public-registration role: member.
 * Existing role assignments are never modified or replaced.
 */
final class DefaultMemberRoleAssigner
{
    public function assignIfUnassigned(User $user): bool
    {
        if (! $user->exists) {
            return false;
        }

        return DB::transaction(static function () use ($user): bool {
            // Lock the account row to serialize normal concurrent assignments.
            $existingUser = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->first();

            if ($existingUser === null) {
                return false;
            }

            $userId = $existingUser->getKey();

            if (DB::table('user_roles')->where('user_id', $userId)->exists()) {
                return false;
            }

            // A minimal, safe catalog fallback for an unseeded environment.
            // Full permission grants still require RbacPermissionSeeder.
            // This can never create an administrative role or permission.
            $now = now();

            DB::table('roles')->insertOrIgnore([
                'code' => 'member',
                'label' => 'Member',
                'description' => 'Pelajar atau mahasiswa pengguna MENTORA.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return DB::table('user_roles')->insertOrIgnore([
                'user_id' => $userId,
                'role_code' => 'member',
                'assigned_at' => $now,
                'assigned_by' => null,
            ]) === 1;
        });
    }
}
