<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Scholarship\Presentation\Http\Controllers\ScholarshipCatalogController;
use App\Modules\Scholarship\Presentation\Http\Controllers\ScholarshipDetailController;


Route::inertia('/', 'Mentora/Home')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';


/**
 * Preview lokal halaman Detail Beasiswa.
 *
 * Route ini hanya tersedia pada APP_ENV=local.
 * Data contoh dikirim langsung ke Inertia dan
 * tidak disimpan ke database PostgreSQL.
 */
Route::get('/ui-preview/scholarship-detail', function () {
    return \Inertia\Inertia::render(
        'Mentora/Scholarships/Show',
        [
            'preview' => true,

            'scholarship' => [
                'id' => 0,
                'title' => 'Contoh Program Beasiswa MENTORA',
                'slug' => 'contoh-preview-detail',
                'provider_name' => 'Penyelenggara Contoh (Bukan Program Resmi)',

                'summary' => 'Halaman ini digunakan untuk memeriksa desain dan kelengkapan komponen Detail Beasiswa MENTORA.',

                'description' => 'Program contoh untuk pengujian antarmuka pengguna. Seluruh informasi pada halaman ini merupakan data demonstrasi dan bukan pengumuman beasiswa yang sebenarnya.',

                'education_levels' => ['S1', 'S2'],
                'fields_of_study' => ['sains-teknologi'],
                'location_scope' => 'indonesia',
                'funding_type' => 'fully_funded',

                // Tanggal relatif agar status periode
                // dapat diperiksa saat preview dilakukan.
                'opens_at' => now()->subDays(7)->toDateString(),
                'closes_at' => now()->addDays(21)->toDateString(),

                'requirements' => [
                    'Contoh persyaratan jenjang pendidikan.',
                    'Contoh persyaratan akademik.',
                    'Contoh persyaratan administrasi.',
                ],

                'benefits' => [
                    'Contoh bantuan biaya pendidikan.',
                    'Contoh bantuan biaya penunjang studi.',
                    'Contoh kegiatan pembinaan penerima.',
                ],

                'required_documents' => [
                    'Contoh dokumen identitas.',
                    'Contoh dokumen akademik.',
                    'Contoh dokumen pendukung.',
                ],

                // Tidak menggunakan URL fiktif
                // yang menyerupai tautan pendaftaran resmi.
                'source_url' => 'https://example.org/',
                'application_url' => null,
            ],
        ]
    );
})->name('ui-preview.scholarship-detail');


Route::get('/beasiswa', ScholarshipCatalogController::class)
    ->name('scholarships.index');

    // Halaman detail beasiswa untuk pengunjung publik.
Route::get('/beasiswa/{slug}', ScholarshipDetailController::class)
    ->name('scholarships.show');