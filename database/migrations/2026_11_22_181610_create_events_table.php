<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Identitas kegiatan.
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('organizer_name', 160);

            // Informasi kegiatan.
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();

            // Jenis dan metode pelaksanaan.
            $table->string('event_type', 30);
            $table->string('delivery_mode', 20);
            $table->string('location_name', 255)->nullable();

            // Jadwal disimpan sebagai timestamp berzona waktu.
            $table->string('timezone', 64)
                ->default('Asia/Jakarta');

            $table->dateTimeTz('starts_at');
            $table->dateTimeTz('ends_at')->nullable();
            $table->dateTimeTz('registration_closes_at')
                ->nullable();

            // Informasi dari penyelenggara.
            $table->string('source_url', 2048);
            $table->string('registration_url', 2048)
                ->nullable();

            // Poster event, bukan file gambar itu sendiri.
            $table->string('cover_image_path', 1024)
                ->nullable();

            // Pengendalian publikasi.
            $table->string('publication_status', 20)
                ->default('draft');

            $table->dateTimeTz('published_at')->nullable();

            $table->timestamps();

            // Mendukung pencarian event publik.
            $table->index([
                'publication_status',
                'starts_at',
            ]);

            // Mendukung filter jenis dan metode event.
            $table->index([
                'event_type',
                'delivery_mode',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};