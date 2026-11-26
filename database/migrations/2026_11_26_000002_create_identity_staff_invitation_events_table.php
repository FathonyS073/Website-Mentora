<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_invitation_events', function (Blueprint $table): void {
            $table->id();
            // Preserve the audit row even when an account is later removed.
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role_code', 48);
            // created, mail_sent, mail_failed, resent, resend_failed.
            $table->string('event', 32);
            $table->timestamps();

            $table->index(['target_user_id', 'event', 'created_at'], 'staff_inv_target_event_time_idx');
            $table->index(['actor_user_id', 'created_at'], 'staff_inv_actor_time_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_invitation_events');
    }
};
