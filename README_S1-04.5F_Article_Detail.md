# MENTORA — S1-04.5F: Detail Artikel dan Preview Lokal

## Ruang lingkup

Paket memperbarui **tiga file**:

1. `resources/js/pages/Mentora/Articles/Show.vue` — desain Detail Artikel, tetap menggunakan PublicLayout.
2. `resources/js/components/mentora/ArticleCard.vue` — kartu asli tetap menuju `/artikel/{slug}`, sementara kartu demo menuju `/ui-preview/article-detail/{sample}`.
3. `routes/mentora-article-preview.php` — **mempertahankan** route lama `/ui-preview/article-cards`, sekaligus menambah route baru `/ui-preview/article-detail/{sample}` untuk 8 contoh (hanya `APP_ENV=local`).

**Tidak mengubah** `routes/web.php`, `resources/js/app.ts`, database, migration, controller, repository, Navbar, Login/Register, Event, atau Katalog Artikel (`Index.vue`).

## Langkah 1: Backup (PowerShell dari root proyek)

```powershell
Set-Location "D:\Project Website\MENTORA"
Copy-Item "resources\js\pages\Mentora\Articles\Show.vue" "$env:TEMP\MENTORA_Article_Show_S1-04.5D_Backup.vue" -Force
Copy-Item "resources\js\components\mentora\ArticleCard.vue" "$env:TEMP\MENTORA_ArticleCard_S1-04.5E_Backup.vue" -Force
Copy-Item "routes\mentora-article-preview.php" "$env:TEMP\MENTORA_ArticlePreview_S1-04.5E_Backup.php" -Force
```

## Langkah 2: Instal ZIP

Unduh `MENTORA_Article_Detail_S1-04.5F.zip` ke `Downloads`.

```powershell
Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Article_Detail_S1-04.5F.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

**Tidak ada perubahan manual pada route**. Pada tahap S1-04.5E Anda sudah menambahkan satu baris pada `routes/web.php`:

```php
require __DIR__ . '/mentora-article-preview.php';
```

Jangan tambahkan lagi. Pastikan file route terpasang dari ZIP ini.

## Langkah 3: Verifikasi

```powershell
php artisan route:clear
php artisan route:list --path=ui-preview
php artisan test
npm run build
```

Periksa route baru `ui-preview/article-detail/{sample}` dan route lama `ui-preview/article-cards`.

### Alamat browser

- **Preview katalog:** http://127.0.0.1:8000/ui-preview/article-cards
- **Preview detail contoh:** http://127.0.0.1:8000/ui-preview/article-detail/contoh-panduan-beasiswa
- **Katalog asli:** http://127.0.0.1:8000/artikel
- **Detail asli:** `/artikel/{slug}` hanya untuk artikel dengan status `published`; draft dan slug tidak ditemukan tetap 404.

Klik **Baca Contoh** pada salah satu dari 7 kartu daftar preview, lalu coba kembali ke preview. Semua 8 slug contoh dapat dibuka, termasuk artikel unggulan melalui URL-nya. Contoh tidak dimasukkan ke PostgreSQL.

## Keamanan dan desain

- Detail asli tidak berubah rutenya. Artikel draft tetap tidak dapat dibuka publik.
- Preview menggunakan data contoh saja dan hanya aktif pada `APP_ENV=local`.
- Konten artikel dirender sebagai teks Vue yang aman (bukan `v-html`). Paragraf dipisahkan dengan baris kosong.
- Sampul hanya menerima HTTPS atau path `/storage/...` dan relative public-disk yang aman.
- URL sumber hanya ditampilkan untuk protokol http/https; sumber dinonaktifkan di preview.
- Tidak menampilkan nama penulis fiktif dan tidak membuat artikel real.
- Jangan menyajikan data preview dalam situs produksi.

## Rollback (jika diperlukan)

```powershell
Copy-Item "$env:TEMP\MENTORA_Article_Show_S1-04.5D_Backup.vue" "resources\js\pages\Mentora\Articles\Show.vue" -Force
Copy-Item "$env:TEMP\MENTORA_ArticleCard_S1-04.5E_Backup.vue" "resources\js\components\mentora\ArticleCard.vue" -Force
Copy-Item "$env:TEMP\MENTORA_ArticlePreview_S1-04.5E_Backup.php" "routes\mentora-article-preview.php" -Force
php artisan route:clear
```

**Target regresi:** 76 tests passed (419 assertions), bila tidak ada perubahan proyek lain.

Setelah screenshot Detail Artikel sesuai, tahap berikutnya S1-04.5G: final QA, build, dan Git checkpoint.
