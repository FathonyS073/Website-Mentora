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


    // S1-04.5F: halaman detail contoh, hanya tersedia saat APP_ENV=local.
    // Slug diambil dari delapan kartu contoh pada route katalog di atas.
    Route::get('/ui-preview/article-detail/{sample}', static function (string $sample) {
        $samples = [
            'contoh-panduan-beasiswa' => [
                'id' => -1,
                'slug' => 'contoh-panduan-beasiswa',
                'title' => '[CONTOH] Panduan Lengkap Persiapan Beasiswa',
                'category' => 'Tips Beasiswa',
                'excerpt' => 'Ringkasan demonstrasi mengenai tahapan persiapan, dokumen, dan strategi memilih program beasiswa.',
                'published_at' => '2027-02-10T02:00:00Z',
                'content' => implode("\n\n", [
                    'Memahami Kebutuhan dan Persyaratan',
                    'Sebelum memilih program, baca persyaratan umum, jenjang pendidikan yang dituju, serta jadwal pendaftarannya. Simpan tautan resmi untuk mengecek setiap perubahan.',
                    'Menyiapkan Dokumen',
                    'Buat daftar dokumen yang diminta lalu siapkan berkas sesuai format dan ukuran yang ditetapkan. Perhatikan tanggal berlaku serta konsistensi data.',
                    'Meninjau Kembali Pendaftaran',
                    'Sebelum mengirim, periksa seluruh isian, lampiran, dan batas waktu. Ini hanya contoh panduan dan bukan pengumuman beasiswa yang sebenarnya.',
                ]),
            ],
            'contoh-menulis-esai' => [
                'id' => -2,
                'slug' => 'contoh-menulis-esai',
                'title' => '[CONTOH] Tips Menulis Esai Beasiswa yang Meyakinkan',
                'category' => 'Tips Beasiswa',
                'excerpt' => 'Contoh cara menyusun tujuan studi dan alasan pendaftaran agar tulisan lebih terarah.',
                'published_at' => '2027-02-09T02:00:00Z',
                'content' => implode("\n\n", [
                    'Merumuskan Gagasan',
                    'Tentukan pesan utama yang ingin disampaikan, seperti tujuan studi, pengalaman relevan, dan kontribusi setelah memperoleh kesempatan belajar.',
                    'Menyusun Alur yang Jelas',
                    'Gunakan pembuka yang relevan, uraian pengalaman berbasis contoh, dan penutup yang menjelaskan arah pengembangan diri.',
                    'Memeriksa Keaslian',
                    'Pastikan tulisan mencerminkan pengalaman sendiri, memenuhi ketentuan program, dan telah diperiksa ejaan serta keterbacaannya.',
                ]),
            ],
            'contoh-dokumen' => [
                'id' => -3,
                'slug' => 'contoh-dokumen',
                'title' => '[CONTOH] Menyiapkan Berkas Pendaftaran Beasiswa',
                'category' => 'Panduan Pendaftaran',
                'excerpt' => 'Daftar ilustratif dokumen dan cara mengatur berkas sebelum pengajuan.',
                'published_at' => '2027-02-07T02:00:00Z',
                'content' => implode("\n\n", [
                    'Membuat Daftar Periksa',
                    'Catat semua lampiran yang dibutuhkan, misalnya identitas, bukti pendidikan, dan dokumen pendukung sesuai ketentuan penyelenggara.',
                    'Menata Berkas',
                    'Beri nama berkas secara konsisten dan simpan salinan cadangan. Pastikan isi dokumen terbaca serta ukuran dan formatnya sesuai persyaratan.',
                    'Validasi Sebelum Kirim',
                    'Cocokkan data pada formulir dengan dokumen pendukung untuk mengurangi kesalahan administratif.',
                ]),
            ],
            'contoh-wawancara' => [
                'id' => -4,
                'slug' => 'contoh-wawancara',
                'title' => '[CONTOH] Strategi Menjawab Pertanyaan Wawancara',
                'category' => 'Persiapan Seleksi',
                'excerpt' => 'Latihan menjawab pertanyaan mengenai motivasi, kontribusi, dan rencana masa depan.',
                'published_at' => '2027-02-06T02:00:00Z',
                'content' => implode("\n\n", [
                    'Memahami Materi Wawancara',
                    'Pelajari tujuan program beasiswa dan siapkan jawaban tentang alasan memilih bidang studi serta pengalaman yang relevan.',
                    'Berlatih Menjelaskan Pengalaman',
                    'Gunakan contoh yang jelas ketika menceritakan tantangan, tindakan yang diambil, dan pembelajaran yang diperoleh.',
                    'Mengevaluasi Latihan',
                    'Coba latihan tanya jawab dengan teman, catat bagian yang belum jelas, dan perbaiki cara penyampaiannya.',
                ]),
            ],
            'contoh-mendaftar' => [
                'id' => -5,
                'slug' => 'contoh-mendaftar',
                'title' => '[CONTOH] Langkah-Langkah Mendaftar Beasiswa',
                'category' => 'Panduan Pendaftaran',
                'excerpt' => 'Alur demonstrasi mulai dari membaca persyaratan hingga melengkapi aplikasi.',
                'published_at' => '2027-02-05T02:00:00Z',
                'content' => implode("\n\n", [
                    'Membaca Pengumuman Resmi',
                    'Pastikan program, periode pendaftaran, sasaran penerima, dan cara pengiriman dokumen berasal dari kanal penyelenggara.',
                    'Mengisi Formulir',
                    'Siapkan data yang diperlukan, isi bagian wajib dengan teliti, lalu unggah dokumen pendukung yang sesuai.',
                    'Menyimpan Bukti',
                    'Setelah mengirim, simpan nomor pendaftaran atau bukti penerimaan jika tersedia, dan pantau status melalui kanal resmi.',
                ]),
            ],
            'contoh-kebiasaan' => [
                'id' => -6,
                'slug' => 'contoh-kebiasaan',
                'title' => '[CONTOH] Keterampilan untuk Mengembangkan Diri',
                'category' => 'Pengembangan Diri',
                'excerpt' => 'Contoh kebiasaan belajar, komunikasi, dan manajemen waktu selama mempersiapkan beasiswa.',
                'published_at' => '2027-02-04T02:00:00Z',
                'content' => implode("\n\n", [
                    'Membangun Rutinitas',
                    'Tentukan target belajar mingguan yang realistis dan alokasikan waktu untuk mengevaluasi kemajuan.',
                    'Mengembangkan Komunikasi',
                    'Latih cara menjelaskan ide secara ringkas, mendengarkan masukan, dan menyusun pertanyaan yang relevan.',
                    'Menjaga Konsistensi',
                    'Gunakan catatan sederhana untuk memantau tugas, mengidentifikasi hambatan, serta menyesuaikan rencana bila diperlukan.',
                ]),
            ],
            'contoh-info' => [
                'id' => -7,
                'slug' => 'contoh-info',
                'title' => '[CONTOH] Mengikuti Informasi Program Pendidikan',
                'category' => 'Info & Update',
                'excerpt' => 'Cara mengecek sumber resmi, memahami tenggat, dan mencatat informasi penting.',
                'published_at' => '2027-02-03T02:00:00Z',
                'content' => implode("\n\n", [
                    'Mengecek Sumber Informasi',
                    'Utamakan pengumuman penyelenggara dan periksa apakah laman atau akun yang digunakan benar-benar resmi.',
                    'Memahami Tenggat',
                    'Catat tanggal mulai, penutupan, dan tahapan seleksi berdasarkan zona waktu yang disebutkan.',
                    'Mencatat Pembaruan',
                    'Simpan riwayat perubahan penting dan periksa kembali syarat terbaru sebelum mengirim pendaftaran.',
                ]),
            ],
            'contoh-kisah' => [
                'id' => -8,
                'slug' => 'contoh-kisah',
                'title' => '[CONTOH] Cerita Perjalanan Menuju Impian Studi',
                'category' => 'Kisah Inspiratif',
                'excerpt' => 'Kisah ilustratif tentang menyusun target belajar, membangun kepercayaan diri, dan terus mencoba.',
                'published_at' => '2027-02-02T02:00:00Z',
                'content' => implode("\n\n", [
                    'Memulai dari Tujuan Kecil',
                    'Seorang pelajar dalam cerita ilustratif ini menyusun rencana belajar dan daftar keterampilan yang ingin dikembangkan.',
                    'Menjadi Lebih Siap',
                    'Dengan mencatat kemajuan dan menerima masukan, ia perlahan memperbaiki persiapan administrasi serta kemampuannya.',
                    'Menghargai Proses',
                    'Kisah ini fiktif dan digunakan hanya untuk menunjukkan gaya penyajian artikel, bukan pengalaman penerima beasiswa sebenarnya.',
                ]),
            ],
        ];

        abort_unless(array_key_exists($sample, $samples), 404);

        return Inertia::render('Mentora/Articles/Show', [
            'article' => array_merge([
                'source_url' => null,
                'cover_image_path' => null,
                'author_id' => null,
            ], $samples[$sample]),
            'preview' => true,
        ]);
    })->where('sample', '[a-z0-9-]+')->name('ui-preview.article-detail');
}
