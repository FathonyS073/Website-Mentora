<?php

/**
 * S1-04.5E: Preview antarmuka katalog Article.
 * Tidak membuat data di database dan tidak terdaftar di luar APP_ENV=local.
 */

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

if (app()->environment('local')) {
    Route::get('/ui-preview/article-cards', static function () {
        $articles = [
            [
                'id' => -1,
                'slug' => 'contoh-panduan-beasiswa',
                'title' => '[CONTOH] Panduan Lengkap Persiapan Beasiswa',
                'category' => 'Tips Beasiswa',
                'excerpt' => 'Ringkasan demonstrasi mengenai tahapan persiapan, dokumen, dan strategi memilih program beasiswa.',
                'published_at' => '2027-02-10T02:00:00Z',
            ],
            [
                'id' => -2,
                'slug' => 'contoh-menulis-esai',
                'title' => '[CONTOH] Tips Menulis Esai Beasiswa yang Meyakinkan',
                'category' => 'Tips Beasiswa',
                'excerpt' => 'Contoh cara menyusun tujuan studi dan alasan pendaftaran agar tulisan lebih terarah.',
                'published_at' => '2027-02-09T02:00:00Z',
            ],
            [
                'id' => -3,
                'slug' => 'contoh-dokumen',
                'title' => '[CONTOH] Menyiapkan Berkas Pendaftaran Beasiswa',
                'category' => 'Panduan Pendaftaran',
                'excerpt' => 'Daftar ilustratif dokumen dan cara mengatur berkas sebelum pengajuan.',
                'published_at' => '2027-02-07T02:00:00Z',
            ],
            [
                'id' => -4,
                'slug' => 'contoh-wawancara',
                'title' => '[CONTOH] Strategi Menjawab Pertanyaan Wawancara',
                'category' => 'Persiapan Seleksi',
                'excerpt' => 'Latihan menjawab pertanyaan mengenai motivasi, kontribusi, dan rencana masa depan.',
                'published_at' => '2027-02-06T02:00:00Z',
            ],
            [
                'id' => -5,
                'slug' => 'contoh-mendaftar',
                'title' => '[CONTOH] Langkah-Langkah Mendaftar Beasiswa',
                'category' => 'Panduan Pendaftaran',
                'excerpt' => 'Alur demonstrasi mulai dari membaca persyaratan hingga melengkapi aplikasi.',
                'published_at' => '2027-02-05T02:00:00Z',
            ],
            [
                'id' => -6,
                'slug' => 'contoh-kebiasaan',
                'title' => '[CONTOH] Keterampilan untuk Mengembangkan Diri',
                'category' => 'Pengembangan Diri',
                'excerpt' => 'Contoh kebiasaan belajar, komunikasi, dan manajemen waktu selama mempersiapkan beasiswa.',
                'published_at' => '2027-02-04T02:00:00Z',
            ],
            [
                'id' => -7,
                'slug' => 'contoh-info',
                'title' => '[CONTOH] Mengikuti Informasi Program Pendidikan',
                'category' => 'Info & Update',
                'excerpt' => 'Cara mengecek sumber resmi, memahami tenggat, dan mencatat informasi penting.',
                'published_at' => '2027-02-03T02:00:00Z',
            ],
            [
                'id' => -8,
                'slug' => 'contoh-kisah',
                'title' => '[CONTOH] Cerita Perjalanan Menuju Impian Studi',
                'category' => 'Kisah Inspiratif',
                'excerpt' => 'Kisah ilustratif tentang menyusun target belajar, membangun kepercayaan diri, dan terus mencoba.',
                'published_at' => '2027-02-02T02:00:00Z',
            ],
        ];

        return Inertia::render('Mentora/Articles/Index', [
            'articles' => [
                'items' => $articles,
                'total' => count($articles),
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 12,
            ],
            'filters' => [
                'search' => '',
                'category' => '',
            ],
            'preview' => true,
        ]);
    })->name('ui-preview.article-cards');
}
