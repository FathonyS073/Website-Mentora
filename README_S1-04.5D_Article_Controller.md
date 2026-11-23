# MENTORA — S1-04.5D Controller, Routing & Halaman Dasar Artikel

Paket ini **hanya menambah lima file baru** (2 controller, 2 Vue, 1 feature test), tidak menimpa `routes/web.php`, `resources/js/app.ts`, navbar, atau halaman yang sebelumnya disetujui.

## 1. Ekstrak ZIP ke root proyek

```powershell
Set-Location "D:\Project Website\MENTORA"
Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Article_Controller_S1-04.5D.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

## 2. Edit `routes/web.php` — HANYA sisipkan dua route

Tambahkan pada kelompok `use` di bagian atas file:

```php
use App\Modules\Article\Presentation\Http\Controllers\ArticleCatalogController;
use App\Modules\Article\Presentation\Http\Controllers\ArticleDetailController;
```

Tambahkan kedua route berikut **di luar** blok `if (app()->environment('local'))` agar dapat diakses publik dalam environment non-local. Letakkan katalog sebelum detail:

```php
Route::get('/artikel', ArticleCatalogController::class)->name('articles.index');
Route::get('/artikel/{slug}', ArticleDetailController::class)->name('articles.show');
```

Jangan mengganti seluruh `web.php`. Semua route Scholarship, Event, Features, serta `/ui-preview/*` harus dipertahankan. Jangan menempatkan route `/artikel` di dalam blok preview local.

## 3. Perbaiki pengecualian AppLayout di `resources/js/app.ts`

File `Index.vue` dan `Show.vue` sudah menyertakan `<PublicLayout>` sendiri. Pada kondisi `case name === ...` yang saat ini mengecualikan `Mentora/Events/Index` dan `Mentora/Events/Show` dari `AppLayout`, **tambahkan dua nama halaman** dengan gaya dan sintaks persis seperti yang sudah ada:

- `Mentora/Articles/Index`
- `Mentora/Articles/Show`

Jangan mengganti seluruh `app.ts`. Jika tidak dikecualikan, halaman artikel dapat memperoleh layout ganda.

## 4. Verifikasi route dan tes

```powershell
php artisan route:clear
php artisan route:list --path=artikel
php artisan test tests/Feature/Modules/Article/ArticlePublicRoutesTest.php
php artisan test
npm run build
```

Target pengujian paket ini: 9 feature tests baru. Jika suite sebelumnya 67 tests / 333 assertions, total menjadi **76 tests** (jumlah assertions bertambah). Uji build memverifikasi halaman Inertia Vue.

## 5. Uji browser

- `http://127.0.0.1:8000/artikel` → katalog kosong itu normal karena tidak menambahkan data fiktif ke database.
- `http://127.0.0.1:8000/artikel/tidak-ada` → 404 memang benar.

**Jangan aktifkan navbar Artikel sebelum halaman `/artikel` diverifikasi.** Setelah lolos, kita lanjut ke S1-04.5E: desain katalog Artikel dan preview lokal tanpa menyimpan artikel fiktif ke PostgreSQL.

## Catatan keamanan

- Repository hanya mengembalikan artikel `publication_status = published`.
- Konten di Vue dirender sebagai teks yang di-escape oleh Vue, **bukan** HTML mentah.
- URL sumber hanya diizinkan untuk protokol `http` atau `https`.
- Pencarian dan kategori divalidasi pada controller sebelum diteruskan ke use case.
