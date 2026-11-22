<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            // Identitas artikel.
            $table->string('title', 200);
            $table->string('slug', 220)->unique();

            // Pengelompokan dan isi artikel.
            $table->string('category', 60);
            $table->text('excerpt')->nullable();
            $table->longText('content');

            // Gambar sampul dan sumber pendukung.
            $table->string('cover_image_path', 1024)
                ->nullable();
            $table->string('source_url', 2048)
                ->nullable();

            // Akun penulis artikel.
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Artikel baru tidak langsung dipublikasikan.
            $table->string('publication_status', 20)
                ->default('draft');
            $table->dateTimeTz('published_at')->nullable();

            $table->timestamps();

            // Mendukung pencarian artikel publik.
            $table->index([
                'publication_status',
                'published_at',
            ]);

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};