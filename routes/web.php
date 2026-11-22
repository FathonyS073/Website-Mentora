<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Scholarship\Presentation\Http\Controllers\ScholarshipCatalogController;
use App\Modules\Scholarship\Presentation\Http\Controllers\ScholarshipDetailController;
use App\Modules\Event\Presentation\Http\Controllers\EventCatalogController;
use App\Modules\Event\Presentation\Http\Controllers\EventDetailController;


Route::inertia('/', 'Mentora/Home')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

/**
 * Route preview khusus lingkungan development.
 * Tidak tersedia ketika APP_ENV=production.
 */
if (app()->environment('local')) {

    // Preview tampilan navbar, layout, dan footer.
    Route::inertia(
        '/ui-preview/public',
        'Mentora/PublicLayoutPreview'
    )->name('ui-preview.public');

    // Preview kartu beasiswa.
    Route::inertia(
        '/ui-preview/scholarship-cards',
        'Mentora/Scholarships/CardPreview'
    )->name('ui-preview.scholarship-cards');

    // Route preview detail beasiswa yang sudah ada
    // harus berada di dalam blok ini.
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
    Route::inertia(
        '/ui-preview/event-cards',
        'Mentora/Events/CardPreview'
    )->name('ui-preview.event-cards');
} // Akhir blok route preview khusus local.


Route::get('/beasiswa', ScholarshipCatalogController::class)
    ->name('scholarships.index');

    // Halaman detail beasiswa untuk pengunjung publik.
Route::get('/beasiswa/{slug}', ScholarshipDetailController::class)
    ->name('scholarships.show');

Route::inertia('/fitur', 'Mentora/Features/Index')
    ->name('features.index');

/*
|--------------------------------------------------------------------------
| Event Routes
|--------------------------------------------------------------------------
| Halaman daftar dan detail Event MENTORA.
| Dapat diakses tanpa login.
*/

// Daftar Event.
Route::get('/event', EventCatalogController::class)
    ->name('events.index');

// Detail Event berdasarkan slug.
Route::get('/event/{slug}', EventDetailController::class)
    ->name('events.show');