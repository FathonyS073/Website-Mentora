<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->string('code', 48)->primary();
            $table->string('label', 100);
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(true);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table): void {
            $table->string('code', 100)->primary();
            $table->string('label', 150);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table): void {
            $table->string('role_code', 48);
            $table->string('permission_code', 100);
            $table->primary(['role_code', 'permission_code']);

            $table->foreign('role_code')
                ->references('code')->on('roles')
                ->cascadeOnDelete();
            $table->foreign('permission_code')
                ->references('code')->on('permissions')
                ->cascadeOnDelete();
        });

        Schema::create('user_roles', function (Blueprint $table): void {
            // MENTORA currently uses Laravel's integer User ID.
            // Do not use the proposed UUID-only reference schema here.
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('role_code', 48);
            $table->timestampTz('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->primary(['user_id', 'role_code']);
            $table->foreign('role_code')
                ->references('code')->on('roles')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
