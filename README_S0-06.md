# MENTORA S0-06 — Public Layout Foundation

## Tujuan
Menambah navbar, footer, layout publik, dan halaman pratinjau terpisah di Laravel 13 + Vue 3 + Inertia + Tailwind CSS 4.
**Tidak mengubah** `routes/web.php`, `Welcome.vue`, autentikasi Fortify, atau halaman dashboard otomatis.

## File baru
- `resources/js/components/mentora/MentoraBrand.vue`
- `resources/js/components/mentora/PublicNavbar.vue`
- `resources/js/components/mentora/PublicFooter.vue`
- `resources/js/layouts/PublicLayout.vue`
- `resources/js/pages/Mentora/PublicLayoutPreview.vue`

## Prasyarat
- `resources/css/mentora-theme.css` sudah di-import oleh `resources/css/app.css` (telah diperiksa melalui keluaran yang diberikan).
- `resources/js/components/mentora/MentoraButton.vue` dan `MentoraCard.vue` sudah tersedia.
- Route `login`, `register`, `dashboard` dihasilkan oleh Laravel Wayfinder.

## Pasang
1. Pastikan file tujuan belum ada (`Test-Path` untuk file baru).
2. Ekstrak ZIP ke folder `D:\Project Website\MENTORA` (tanpa overwrite).
3. **Buka** `routes/web.php` dan **tambahkan** potongan berikut di akhir, tanpa menghapus route yang ada:

```php
// Preview internal khusus lingkungan lokal; bukan route publik produksi.
if (app()->environment('local')) {
    Route::inertia('/ui-preview/public', 'Mentora/PublicLayoutPreview')
        ->name('ui-preview.public');
}
```

4. Jalankan `php artisan route:list --name=ui-preview.public`.
5. Jalankan `npm run build`, kemudian `php artisan test`.
6. Jalankan `php artisan serve`, kunjungi `http://127.0.0.1:8000/ui-preview/public`.

Jika environment APP_ENV bukan `local`, route preview memang tidak tersedia. **Jangan mengubah APP_ENV hanya untuk memaksa preview di production.**

## Keputusan desain dan keamanan
- Visual mengikuti warna dan komposisi rujukan Mentora; ini **hanya struktur awal** dan halaman preview, **bukan Homepage final**.
- Menu selain Beranda sengaja dibuat nonaktif karena route `beasiswa`, `fitur`, `event`, `artikel`, `tentang` belum dibuat. Jangan buat link 404.
- Masuk, Daftar Gratis, dan Dashboard memanfaatkan route autentikasi yang sudah tersedia.
- Simbol M pada logo adalah **placeholder vektor**; ganti dengan file logo brand resmi ketika tersedia.
- Tidak ada data beasiswa, angka statistik atau testimoni palsu yang ditampilkan.
- Preview untuk local environment saja. Hapus route preview sebelum production jika tidak diperlukan.
- Tidak ada perubahan pada `.env`, `.env.docker`, tabel database, atau service provider.

## Rollback
Hapus kelima file baru dan hapus blok route `ui-preview.public` yang ditambahkan ke `routes/web.php`. Jangan menghapus file dasar yang sudah ada.
