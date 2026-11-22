# MENTORA — S1-04.5B: Struktur Modul Article

Paket ini membuat modul `app/Modules/Article/` dengan struktur Modular Monolith yang sama dengan Scholarship dan Event.

## Isi paket

- `Infrastructure/Models/Article.php` — Eloquent Model untuk tabel `articles` yang sudah dimigrasi.
- `Domain/Contracts/ArticleRepository.php` — kontrak repository.
- `Infrastructure/Repositories/EloquentArticleRepository.php` — pencarian, filter kategori, pagination, detail slug; hanya artikel berstatus `published`.
- `ArticleServiceProvider.php` — binding dependency injection.
- `Application/UseCases/` dan `Presentation/Http/Controllers/` — folder siap diisi pada tahap berikutnya.

## Pemasangan

Jalankan di PowerShell:

```powershell
Set-Location "D:\Project Website\MENTORA"

Expand-Archive `
    -Path "$env:USERPROFILE\Downloads\MENTORA_Article_Module_S1-04.5B.zip" `
    -DestinationPath "D:\Project Website\MENTORA" `
    -Force
```

> Opsi `-Force` menimpa file dengan path yang sama. Jika pernah membuat sendiri file Article, cadangkan terlebih dahulu.

## SATU PERUBAHAN MANUAL: daftarkan provider

Buka `bootstrap/providers.php` dan tambahkan satu entri ke dalam array tanpa menghapus provider yang sudah ada:

```php
App\Modules\Article\ArticleServiceProvider::class,
```

Jangan membuat ulang migration `create_articles_table` karena sudah **Ran (Batch 4)**.

## Verifikasi

```powershell
Get-ChildItem "app\Modules\Article" -Recurse
php -l "app\Modules\Article\Infrastructure\Models\Article.php"
php artisan tinker
```

Di dalam Tinker:

```php
interface_exists(\App\Modules\Article\Domain\Contracts\ArticleRepository::class);
$repository = app(\App\Modules\Article\Domain\Contracts\ArticleRepository::class);
get_class($repository);
$repository->paginatePublished();
$repository->findPublishedBySlug('artikel-yang-belum-ada');
exit
```

Jika data belum ada, `paginatePublished()` menghasilkan `items=[]`, `total=0`, `last_page=1` dan pencarian slug mengembalikan `null`.

Kemudian:

```powershell
php artisan test
```

Target regresi: **62 tests passed** (sebelum pengujian artikel baru ditambahkan).

## Catatan

- Artikel dengan `publication_status=draft` tidak dapat ditampilkan melalui repository publik.
- `published_at` digunakan untuk mengurutkan artikel terbaru, tetapi belum menerapkan penjadwalan publikasi otomatis. Penjadwalan merupakan aturan terpisah yang dapat dibuat ketika Editor CMS dikembangkan.
- Isi `content` sebaiknya ditampilkan sebagai teks biasa yang di-escape oleh Vue. Jangan render HTML mentah dengan `v-html` tanpa sanitasi.
- Saat ini tidak ada data contoh yang ditulis ke PostgreSQL dan tidak ada perubahan pada Homepage, Navbar, Scholarship, atau Event.
