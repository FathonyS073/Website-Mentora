MENTORA - S1-04.4D Event Controller & Routing

Paket ini menambah 5 file tanpa menimpa file proyek yang sudah ada:
- 2 controller PHP pada app/Modules/Event/Presentation/Http/Controllers/
- 2 halaman Vue awal pada resources/js/pages/Mentora/Events/
- 1 file pengujian pada tests/Feature/Modules/Event/

1. Ekstrak zip ke root D:\Project Website\MENTORA (lihat petunjuk ChatGPT).
2. Di routes/web.php, TAMBAHKAN import:
   use App\Modules\Event\Presentation\Http\Controllers\EventCatalogController;
   use App\Modules\Event\Presentation\Http\Controllers\EventDetailController;
   Di luar blok if(APP_ENV local), TAMBAHKAN:
   Route::get('/event', EventCatalogController::class)->name('events.index');
   Route::get('/event/{slug}', EventDetailController::class)->name('events.show');
3. Di resources/js/app.ts, kecualikan kedua halaman dari AppLayout, dengan pola yang sama seperti halaman Mentora/Scholarships/Index dan Mentora/Features/Index:
   Mentora/Events/Index
   Mentora/Events/Show
4. JANGAN aktifkan menu Event pada navbar sebelum halaman /event diverifikasi.
5. Jalankan:
   php artisan route:list --path=event
   php artisan test tests/Feature/Modules/Event/EventPublicRoutesTest.php
   php artisan test
   npm run build   (checkpoint untuk Vue baru)
6. Buka http://127.0.0.1:8000/event (katalog kosong adalah normal).
   Buka http://127.0.0.1:8000/event/tidak-ada (404 adalah normal).

Paket tidak menyertakan data fiktif untuk database PostgreSQL utama. Tidak mengubah file lainnya, Homepage, Katalog Beasiswa, atau layout.
