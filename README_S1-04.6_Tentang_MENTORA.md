# MENTORA — S1-04.6 Halaman Tentang

Paket ini menambahkan **2 file baru** saja:

- `resources/js/pages/Mentora/About.vue` — halaman Tentang MENTORA, responsif, mengikuti referensi desain yang disepakati. Sudah memakai `<PublicLayout>` dan tidak membutuhkan database.
- `tests/Feature/Mentora/AboutPageTest.php` — 2 pengujian halaman publik dan named route.

Paket **tidak menimpa** homepage, beasiswa, event, artikel, login, register, atau file konfigurasi lama. Perubahan integrasi berikut dilakukan secara manual agar aman.

## 1. Ekstrak ZIP

Unduh `MENTORA_About_S1-04.6.zip` ke `Downloads`, lalu jalankan di PowerShell VS Code:

```powershell
Set-Location "D:\Project Website\MENTORA"
Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_About_S1-04.6.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

## 2. Tambahkan SATU route publik ke `routes/web.php`

Tempatkan **di luar** `if (app()->environment('local'))`, misalnya bersama route `/fitur`, `/artikel`, `/event`. Jangan menghapus route lain.

```php
Route::inertia('/tentang', 'Mentora/About')->name('about');
```

PENTING: bila sudah ada route GET `/tentang`, jangan tambahkan duplikat.

## 3. Perbarui `resources/js/app.ts`

Halaman baru sudah menyertakan `<PublicLayout>`, jadi tambahkan `Mentora/About` pada daftar/ekspresi pengecualian `AppLayout`, **dengan pola yang sama** seperti halaman `Mentora/Articles/Show`.

Jika menggunakan `switch (true)` / `case name === ...:`:

```ts
case name === 'Mentora/About':
```

Jika menggunakan operator `||`:

```ts
|| name === 'Mentora/About'
```

Jika menggunakan array `publicPages.includes(name)`, tambahkan string:

```ts
'Mentora/About',
```

Jangan menyalin ketiga contoh sekaligus; gunakan hanya yang sesuai kode aktual. Jangan mengganti isi `app.ts` seluruhnya.

## 4. Aktifkan navbar Tentang

Buka `resources/js/components/mentora/PublicNavbar.vue`. Dalam array `navigation`, cari item dengan `label: 'Tentang'` dan `href: '/tentang'`. Ubah **hanya** `enabled: false` menjadi `enabled: true`, contohnya:

```ts
{ label: 'Tentang', href: '/tentang', enabled: true },
```

Jika sudah `true`, jangan diubah. Jangan mengubah item navbar lain.

## 5. Verifikasi

```powershell
php artisan route:clear
php artisan route:list --path=tentang
php artisan test tests/Feature/Mentora/AboutPageTest.php
php artisan test
npm run build
```

Target: route `GET|HEAD tentang` bernama `about`; **2 pengujian baru** lulus; regresi sebelumnya 76 => total **78 tests** bila tidak ada perubahan lain; build selesai tanpa error.

Buka `http://127.0.0.1:8000/tentang` dan periksa scroll sampai footer. Coba FAQ, link `Visi & Misi`, tombol Katalog Beasiswa, serta navbar dan tampilan mobile.

## Catatan produk dan keamanan

- Tidak ada data fiktif di database maupun route preview baru; halaman ini halaman publik yang sebenarnya.
- Fitur Matching dan Tracker secara eksplisit diberi label **Dalam pengembangan**, tidak dianggap sudah tersedia.
- Tidak menggunakan `v-html` atau HTML yang disisipkan dari pengguna.
- Tidak membuat tautan palsu untuk Kontak, Syarat & Ketentuan, atau Kebijakan Privasi yang belum aktif.
- Jangan `git add .` sebelum meninjau perubahan. Setelah semua tes lolos, lakukan commit secara terarah.
