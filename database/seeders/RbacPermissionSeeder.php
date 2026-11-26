<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Identity\Domain\Permissions\RbacPermissionMatrix;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Idempotently synchronizes only the six internal system roles' permission
 * grants. It NEVER inserts user_roles and cannot create a privileged user.
 */
final class RbacPermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            // Can be run safely after S2-01B, or on a fresh test database.
            $this->call(RbacCatalogSeeder::class);

            $catalog = RbacPermissionMatrix::permissions();
            $grants = RbacPermissionMatrix::grants();
            $timestamp = now();

            $permissionRows = [];
            foreach ($catalog as $code => $permission) {
                $permissionRows[] = [
                    'code' => $code,
                    'label' => $permission['label'],
                    'description' => $permission['description'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            DB::table('permissions')->upsert(
                $permissionRows,
                ['code'],
                ['label', 'description', 'updated_at'],
            );

            foreach ($grants as $roleCode => $permissionCodes) {
                foreach ($permissionCodes as $permissionCode) {
                    if (!array_key_exists($permissionCode, $catalog)) {
                        throw new InvalidArgumentException(
                            "Undefined permission {$permissionCode} for role {$roleCode}"
                        );
                    }
                }

                // Synchronize the declared baseline. Only the six named roles
                // are affected; no other roles and no user_roles are changed.
                DB::table('role_permissions')
                    ->where('role_code', $roleCode)
                    ->whereNotIn('permission_code', $permissionCodes)
                    ->delete();

                DB::table('role_permissions')->insertOrIgnore(
                    array_map(
                        static fn (string $permissionCode): array => [
                            'role_code' => $roleCode,
                            'permission_code' => $permissionCode,
                        ],
                        $permissionCodes,
                    ),
                );
            }
        });
    }
}
