# MENTORA S1-01 — Homepage

Target: `D:\Project Website\MENTORA` (Windows PowerShell).

## Isi ZIP
- `resources/js/pages/Mentora/Home.vue` — halaman Homepage baru, mengikuti gambar LP EdTech MENTORA Modern.
- `tests/Feature/Mentora/HomePageTest.php` — satu tes route publik (PHPUnit).

File ZIP **tidak** menyertakan dan tidak menimpa file `routes/web.php`, `resources/js/app.ts`, `PublicLayout.vue`, `PublicNavbar.vue`, `PublicFooter.vue`, atau file `.env`.

## 1. Ekstrak
Pastikan file `resources/js/pages/Mentora/Home.vue` belum ada, sebelum mengekstrak tanpa `-Force`.

```powershell
Set-Location "D:\Project Website\MENTORA"
Test-Path "resources\js\pages\Mentora\Home.vue"
Expand-Archive -Path "$env:USERPROFILE\Downloads\MENTORA_S1-01_Homepage.zip" -DestinationPath "D:\Project Website\MENTORA"
```

## 2. Edit `routes/web.php`
**Hanya ganti** route awal:

```php
Route::inertia('/', 'Welcome')->name('home');
```

menjadi:

```php
Route::inertia('/', 'Mentora/Home')->name('home');
```

Jangan mengubah route dashboard, settings, ataupun preview.

## 3. Edit `resources/js/app.ts`
Di switch pemilihan layout, tambahkan case `Mentora/Home` pada branch yang mengembalikan `null`, misalnya:

```ts
case name === 'Welcome':
case name === 'Mentora/PublicLayoutPreview':
case name === 'Mentora/Home':
    return null;
```

Karena komponen `Home.vue` sudah membungkus halaman dengan `<PublicLayout>`, pengecualian ini mencegah sidebar dashboard muncul pada halaman publik. Jangan menyentuh branch auth, settings, atau default.

## 4. Uji

```powershell
php artisan route:clear
php artisan test tests/Feature/Mentora/HomePageTest.php
npm run build
php artisan test
```

Pastikan Laravel Server aktif (`php artisan serve`) dan buka http://127.0.0.1:8000/ .

## Catatan penting
- File hero harus tetap ada di `public/Images/Hero-Section.webp` (kapitalisasi tepat).
- Komponen Navbar dan Footer menggunakan yang sudah ada; tidak diduplikasi.
- Katalog asli belum terhubung. Search akan menampilkan pemberitahuan bahwa pencarian belum tersedia dan **tidak** membuat klaim beasiswa fiktif.
- FAQ dan form filter sementara adalah UI frontend. CTA register/dashboard sudah menggunakan generated route Laravel.
- Jangan mengirim `.env` atau kredensial.

## Next
Setelah build/test dan pengecekan visual: responsive QA, penyempurnaan spacing berdasarkan referensi screenshot asli, lalu modul Scholarship/katalog.
