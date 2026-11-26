# MENTORA — S1-04.5E: Katalog Artikel dan Preview Lokal

## Scope / file yang disentuh
- **Diganti:** `resources/js/pages/Mentora/Articles/Index.vue` (desain katalog). 
- **Ditambah:** `resources/js/components/mentora/ArticleCard.vue` (kartu artikel yang dapat digunakan ulang).
- **Ditambah:** `routes/mentora-article-preview.php` (route + data contoh, hanya APP_ENV=local).
- Tidak mengubah `Show.vue`, database, controller, repository, Login/Register, navbar, atau PublicLayout.

## Langkah 1 — Backup halaman yang disetujui sebelumnya

```powershell
Set-Location "D:\Project Website\MENTORA"
Copy-Item "resources\js\pages\Mentora\Articles\Index.vue" "$env:TEMP\MENTORA_Article_Index_S1-04.5D_Backup.vue"
```

## Langkah 2 — Ekstrak ZIP

Unduh file `MENTORA_Article_Catalog_UI_S1-04.5E.zip` ke folder Downloads.

```powershell
Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Article_Catalog_UI_S1-04.5E.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

## Langkah 3 — Tambahkan satu baris pada `routes/web.php`

Di bagian **paling bawah** `routes/web.php` (tanpa menghapus/mengganti route apapun), tambahkan:

```php
// Preview Artikel (file ini sudah melindungi route dengan environment local).
require __DIR__ . '/mentora-article-preview.php';
```

Route ini **otomatis tidak terdaftar di non-local** karena file terpisah menggunakan `if (app()->environment('local'))`.
Jangan menambahkan dua kali; jika `require` sudah ada, biarkan saja.

**Tidak perlu mengubah `resources/js/app.ts`.** Halaman preview menggunakan komponen `Mentora/Articles/Index` yang sama dengan route `/artikel`, dan komponen itu sudah dikecualikan dari `AppLayout` pada tahap S1-04.5D.

## Langkah 4 — Uji

```powershell
php artisan route:clear
php artisan route:list --path=ui-preview
php artisan test
npm run build
```

Di browser:
- `http://127.0.0.1:8000/artikel` -> katalog asli, kemungkinan masih empty state.
- `http://127.0.0.1:8000/ui-preview/article-cards` -> delapan artikel contoh **tanpa menyentuh PostgreSQL**.

Coba filter kategori dan kotak pencarian di halaman preview. Keduanya akan menyaring contoh secara lokal; pada `/artikel` asli, pencarian tetap menggunakan Laravel / Inertia.

### Catatan penting
- Semua kartu preview tidak mengarah ke URL detail palsu (tidak akan menyebabkan 404).
- Belum membuat layanan newsletter karena backend penyimpanan langganan belum ada. CTA diarahkan ke `/beasiswa`.
- Kategori yang ditampilkan sementara mengikuti struktur konten rancangan. Repository tetap dapat mencari kategori lain lewat query `?category=...`.
- Untuk cover image asli, komponen menerima jalur relatif dari disk publik Laravel (misalnya `articles/cover.webp`) atau HTTPS. Jika tidak ada, dipakai ilustrasi gradien.
- Tidak ada data demo yang dibuat di database.
- Desain Detail Artikel final akan dikerjakan terpisah pada **S1-04.5F**.

## Rollback

Jika diperlukan:

```powershell
Copy-Item "$env:TEMP\MENTORA_Article_Index_S1-04.5D_Backup.vue" "resources\js\pages\Mentora\Articles\Index.vue" -Force
```

Hapus baris `require __DIR__ . '/mentora-article-preview.php';` jika ingin menonaktifkan preview lokal. File lain boleh dibiarkan atau dihapus setelah diverifikasi tidak ada referensi.
