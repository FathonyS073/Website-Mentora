MENTORA — S1-04.4E UI/UX Katalog Event
=====================================

ISI PAKET
- resources/js/components/mentora/EventCard.vue (baru)
- resources/js/pages/Mentora/Events/Index.vue (mengganti halaman katalog Event AWAL)
- resources/js/pages/Mentora/Events/CardPreview.vue (baru, preview UI tanpa database)

DESAIN DAN BATASAN
- Mengikuti palet warna MENTORA; hero ilustratif tanpa mengklaim gambar poster resmi.
- Filter search, event_type, delivery_mode, dan pagination tetap menggunakan backend Event yang sudah teruji.
- Kartu real mengarah ke /event/{slug}; preview secara eksplisit tidak memberikan link detail palsu.
- Tidak mengubah backend, route /event dan /event/{slug}, halaman Show, Homepage, Navbar, Scholarship, maupun database.
- Data Event asli kosong: halaman /event akan menampilkan empty state; gunakan preview untuk menilai desain kartu.

INSTALASI (jalankan di PowerShell)
1. Set-Location 'D:\Project Website\MENTORA'
2. Cadangkan Index.vue bila ada perubahan lokal yang ingin disimpan:
   Copy-Item 'resources\js\pages\Mentora\Events\Index.vue' "$env:TEMP\MENTORA_Event_Index_before_S1-04.4E.vue"
3. Ekstrak ZIP ke root MENTORA menggunakan Expand-Archive -Force.

AKTIFKAN PREVIEW DI LINGKUNGAN LOKAL
4. Dalam routes/web.php pada blok if (app()->environment('local')) yang SUDAH ADA, tambahkan:
   Route::inertia('/ui-preview/event-cards', 'Mentora/Events/CardPreview')
       ->name('ui-preview.event-cards');
   Jangan buat blok local baru dan jangan letakkan preview di luar blok local.
5. Dalam resources/js/app.ts, pada kelompok pengecualian AppLayout untuk halaman publik,
   tambahkan 'Mentora/Events/CardPreview' dengan pola yang sama seperti
   'Mentora/Scholarships/CardPreview'. Jangan menghapus halaman lain.

VERIFIKASI
6. php artisan route:list --path=ui-preview
7. php artisan test
8. npm run build
9. Buka http://127.0.0.1:8000/event untuk empty state dan filter.
10. Buka http://127.0.0.1:8000/ui-preview/event-cards untuk 3 kartu contoh.

CATATAN
- Tidak ada data dummy yang dimasukkan ke PostgreSQL.
- Atribut starts_at disajikan dalam WIB di kartu. Data ISO dari Eloquent
  akan dikonversi untuk tampilan menggunakan Intl.DateTimeFormat.
- Tidak ada fitur pengurutan, filter tanggal, pendaftaran internal, atau klaim
  jumlah peserta yang belum ditopang backend.
- Jangan commit sebelum memeriksa semua perubahan dan hasil build.
