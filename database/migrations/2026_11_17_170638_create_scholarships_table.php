<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();

            // Identitas beasiswa
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('provider_name', 160);

            // Informasi program
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();

            // Kriteria beasiswa
            $table->json('education_levels');
            $table->json('fields_of_study')->nullable();
            $table->string('location_scope', 20);
            $table->string('funding_type', 30);

            // Periode pendaftaran
            $table->date('opens_at')->nullable();
            $table->date('closes_at')->nullable();

            // Sumber informasi
            $table->string('source_url', 2048);
            $table->string('application_url', 2048)->nullable();

            // Moderasi dan publikasi
            $table->string('verification_status', 20)
                ->default('pending');

            $table->string('publication_status', 20)
                ->default('draft');

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            // Index untuk katalog
            $table->index([
                'publication_status',
                'verification_status',
            ], 'scholarships_visibility_idx');

            $table->index('closes_at');

            $table->index([
                'location_scope',
                'funding_type',
            ], 'scholarships_filters_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};