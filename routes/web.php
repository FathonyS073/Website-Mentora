<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Scholarship\Presentation\Http\Controllers\ScholarshipCatalogController;
use App\Modules\Scholarship\Presentation\Http\Controllers\ScholarshipDetailController;
use App\Modules\Event\Presentation\Http\Controllers\EventCatalogController;
use App\Modules\Event\Presentation\Http\Controllers\EventDetailController;
use App\Modules\Article\Presentation\Http\Controllers\ArticleCatalogController;
use App\Modules\Article\Presentation\Http\Controllers\ArticleDetailController;


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
    // Khusus local: contoh detail Event, tanpa menulis ke database.
Route::get('/ui-preview/event-detail/{sample}', function (string $sample) {
    $samples = [
        'preview-event-webinar' => [
            'id' => -1,
            'title' => '[CONTOH] Webinar Strategi Mencari Beasiswa',
            'slug' => 'preview-event-webinar',
            'organizer_name' => 'Penyelenggara Contoh A',
            'summary' => 'Webinar demonstrasi untuk memeriksa tampilan informasi dan jadwal.',
            'description' => "Ini adalah uraian contoh halaman detail webinar MENTORA.\nInformasi ini bukan kegiatan nyata atau undangan pendaftaran.",
            'event_type' => 'webinar',
            'delivery_mode' => 'online',
            'location_name' => null,
            'timezone' => 'Asia/Jakarta',
            'starts_at' => '2027-01-15T02:00:00.000Z',
            'ends_at' => '2027-01-15T04:00:00.000Z',
            'registration_closes_at' => '2027-01-14T16:59:00.000Z',
            'source_url' => null,
            'registration_url' => null,
            'cover_image_path' => null,
        ],
        'preview-event-workshop' => [
            'id' => -2,
            'title' => '[CONTOH] Workshop Persiapan Dokumen Beasiswa',
            'slug' => 'preview-event-workshop',
            'organizer_name' => 'Penyelenggara Contoh B',
            'summary' => 'Workshop contoh untuk menguji tampilan kegiatan tatap muka.',
            'description' => 'Konten demonstrasi untuk memeriksa lokasi, metode, dan informasi pendaftaran workshop.',
            'event_type' => 'workshop',
            'delivery_mode' => 'offline',
            'location_name' => 'Lokasi contoh, Indonesia',
            'timezone' => 'Asia/Jakarta',
            'starts_at' => '2027-01-22T06:00:00.000Z',
            'ends_at' => '2027-01-22T09:00:00.000Z',
            'registration_closes_at' => '2027-01-21T16:59:00.000Z',
            'source_url' => null,
            'registration_url' => null,
            'cover_image_path' => null,
        ],
        'preview-event-seminar' => [
            'id' => -3,
            'title' => '[CONTOH] Seminar Pendidikan dan Peluang Studi',
            'slug' => 'preview-event-seminar',
            'organizer_name' => 'Penyelenggara Contoh C',
            'summary' => 'Seminar contoh untuk memeriksa informasi kegiatan hybrid.',
            'description' => 'Informasi demonstrasi untuk pengujian halaman detail dan konsistensi desain MENTORA.',
            'event_type' => 'seminar',
            'delivery_mode' => 'hybrid',
            'location_name' => 'Lokasi demonstrasi',
            'timezone' => 'Asia/Jakarta',
            'starts_at' => '2027-02-05T01:30:00.000Z',
            'ends_at' => '2027-02-05T04:00:00.000Z',
            'registration_closes_at' => '2027-02-04T16:59:00.000Z',
            'source_url' => null,
            'registration_url' => null,
            'cover_image_path' => null,
        ],
    ];

    abort_unless(array_key_exists($sample, $samples), 404);

    return \Inertia\Inertia::render('Mentora/Events/Show', [
        'event' => $samples[$sample],
        'preview' => true,
    ]);
})->name('ui-preview.event-detail');
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
Route::get('/artikel', ArticleCatalogController::class)
    ->name('articles.index');

Route::get('/artikel/{slug}', ArticleDetailController::class)
    ->name('articles.show');
Route::inertia('/tentang', 'Mentora/About')->name('about');

require __DIR__ . '/mentora-article-preview.php';
require __DIR__ . '/mentora-member-dashboard.php';