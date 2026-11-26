<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class RbacCatalogSeeder extends Seeder
{
    /**
     * Establish the six internal role codes, without granting them to users.
     * Guest is an unauthenticated visitor, never a database role.
     */
    public function run(): void
    {
        $roles = [
            ['code' => 'member', 'label' => 'Member', 'description' => 'Pelajar atau mahasiswa pengguna MENTORA.'],
            ['code' => 'guardian', 'label' => 'Guardian', 'description' => 'Orang tua atau wali yang mendampingi Member berdasarkan izin.'],
            ['code' => 'editor', 'label' => 'Editor', 'description' => 'Pengelola draft dan konten informasi.'],
            ['code' => 'verifier', 'label' => 'Verifier', 'description' => 'Pemeriksa dan verifikator informasi.'],
            ['code' => 'admin', 'label' => 'Admin', 'description' => 'Pengelola operasional platform.'],
            ['code' => 'superadmin', 'label' => 'SuperAdmin', 'description' => 'Pengelola akses administratif tingkat tertinggi.'],
        ];

        $timestamp = now();
        $rows = array_map(
            static fn (array $role): array => [
                ...$role,
                'is_system' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            $roles
        );

        DB::table('roles')->upsert(
            $rows,
            ['code'],
            ['label', 'description', 'is_system', 'updated_at']
        );

        // Intentional default-deny: no user_roles or role_permissions writes.
    }
}
