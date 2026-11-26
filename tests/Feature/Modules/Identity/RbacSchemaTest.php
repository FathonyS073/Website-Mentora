<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Identity;

use App\Models\User;
use Database\Seeders\RbacCatalogSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class RbacSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_the_four_expected_rbac_tables(): void
    {
        foreach (['roles', 'permissions', 'role_permissions', 'user_roles'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing RBAC table: {$table}");
        }

        $this->assertTrue(Schema::hasColumn('user_roles', 'assigned_by'));
        $this->assertTrue(Schema::hasColumn('user_roles', 'assigned_at'));
    }

    public function test_seeds_six_roles_idempotently_without_guest(): void
    {
        $this->seed(RbacCatalogSeeder::class);
        $this->seed(RbacCatalogSeeder::class);

        $this->assertSame(
            ['admin', 'editor', 'guardian', 'member', 'superadmin', 'verifier'],
            DB::table('roles')->orderBy('code')->pluck('code')->all()
        );

        $this->assertFalse(DB::table('roles')->where('code', 'guest')->exists());
    }

    public function test_seeding_does_not_grant_roles_or_permissions(): void
    {
        $user = User::factory()->create();
        $this->seed(RbacCatalogSeeder::class);

        $this->assertSame(0, DB::table('user_roles')->where('user_id', $user->getKey())->count());
        $this->assertSame(0, DB::table('role_permissions')->count());
        $this->assertSame(0, DB::table('permissions')->count());
    }

    public function test_user_cannot_have_the_same_role_twice(): void
    {
        $user = User::factory()->create();
        $this->seed(RbacCatalogSeeder::class);

        $assignment = ['user_id' => $user->getKey(), 'role_code' => 'member'];
        DB::table('user_roles')->insert($assignment);

        $this->expectException(QueryException::class);
        DB::table('user_roles')->insert($assignment);
    }

    public function test_permission_cannot_be_assigned_twice_to_a_role(): void
    {
        $this->seed(RbacCatalogSeeder::class);

        DB::table('permissions')->insert([
            'code' => 'profile.view',
            'label' => 'Lihat profil',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $assignment = ['role_code' => 'member', 'permission_code' => 'profile.view'];
        DB::table('role_permissions')->insert($assignment);

        $this->expectException(QueryException::class);
        DB::table('role_permissions')->insert($assignment);
    }
}
