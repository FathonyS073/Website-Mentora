<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menambahkan informasi terstruktur untuk
        // halaman Detail Beasiswa MENTORA.
        Schema::table('scholarships', function (Blueprint $table) {

            // Daftar persyaratan calon penerima beasiswa.
            $table->json('requirements')->nullable();

            // Daftar manfaat atau cakupan pendanaan.
            $table->json('benefits')->nullable();

            // Daftar dokumen yang diperlukan untuk mendaftar.
            $table->json('required_documents')->nullable();
        });
    }

    public function down(): void
    {
        // Membatalkan hanya kolom yang ditambahkan
        // oleh migration ini.
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn([
                'requirements',
                'benefits',
                'required_documents',
            ]);
        });
    }
};