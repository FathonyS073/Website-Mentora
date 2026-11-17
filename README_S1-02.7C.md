# MENTORA — S1-02.7C Kartu Beasiswa

## Isi paket
- `resources/js/components/mentora/ScholarshipCard.vue` — kartu beasiswa horizontal, status pendaftaran dari tanggal, tautan sumber resmi; tombol simpan nonaktif.
- `resources/js/pages/Mentora/Scholarships/Index.vue` — berdasarkan Index_MENTORA_Beasiswa_Fixed.vue tahap sebelumnya; mengganti daftar kartu lama dengan komponen baru tanpa mengubah filter atau logika pencarian.
- `resources/js/pages/Mentora/Scholarships/CardPreview.vue` — **preview lokal** dengan contoh data yang jelas diberi label; tidak mengisi database.

## Instalasi
1. Cadangkan perubahan kerja dengan `git status --short`; pastikan Anda tidak menimpa modifikasi terbaru `Index.vue` yang berbeda dari tahap sebelumnya.
2. Salin dua berkas utama dari paket ke folder relatif yang tercantum.
3. Untuk mengaktifkan preview, di `routes/web.php`, **di dalam** blok `if (app()->environment('local'))` yang sudah ada, tambahkan:

```php
Route::inertia('/ui-preview/scholarship-cards', 'Mentora/Scholarships/CardPreview')
    ->name('ui-preview.scholarship-cards');
```

4. Di `resources/js/app.ts`, pada `layout: (name)` tambahkan halaman `Mentora/Scholarships/CardPreview` ke daftar halaman yang mengembalikan `null` (seperti Mentora/Home). **Jangan** mengubah aturan dashboard, auth, settings.
5. `npm run dev` dibiarkan berjalan. Buka `http://127.0.0.1:8000/ui-preview/scholarship-cards`.
6. Halaman publik `/beasiswa` tetap menampilkan empty state bila database tidak memiliki beasiswa verified+published.

## Pengujian dan batasan
- `php artisan test tests/Feature/Modules/Scholarship/ScholarshipCatalogTest.php`
- `npm run build` dan apabila tersedia `npx vue-tsc --noEmit` sebagai pemeriksaan TypeScript
- **Belum mengaktifkan** halaman detail (S1-03) dan fitur simpan Member.
- Status "dibuka" hanya ditampilkan bila tanggal buka dan tutup sama-sama tersedia dan rentangnya mencakup tanggal lokal hari ini. Data tidak lengkap diberi status netral.
- Logo perusahaan belum tersedia di database, maka yang tampil placeholder ikon, bukan logo buatan.
