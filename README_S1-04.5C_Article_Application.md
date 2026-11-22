# MENTORA — S1-04.5C Application Layer Article

Paket berisi dua Use Case baru dan lima unit test (tanpa mengubah file lama):

- `app/Modules/Article/Application/UseCases/SearchArticles.php`
- `app/Modules/Article/Application/UseCases/GetPublishedArticle.php`
- `tests/Unit/Modules/Article/ArticleUseCasesTest.php`

## 1. Instalasi

Buka PowerShell pada folder proyek MENTORA:

```powershell
Set-Location "D:\Project Website\MENTORA"
Expand-Archive `
    -Path "$env:USERPROFILE\Downloads\MENTORA_Article_Application_S1-04.5C.zip" `
    -DestinationPath "D:\Project Website\MENTORA" `
    -Force
```

Paket ini tidak mengubah migration, route, controller, atau `bootstrap/providers.php`. Pastikan S1-04.5B sebelumnya sudah berhasil.

## 2. Jalankan Unit Test

```powershell
php artisan test tests/Unit/Modules/Article/ArticleUseCasesTest.php
php artisan test
```

Expected unit test: 5 passed. Jika baseline Anda 62 tests / 321 assertions, seluruh suite seharusnya menjadi **67 tests**; jumlah assertions bisa berubah mengikuti pemeriksaan unit test.

## 3. Verifikasi Dependency Injection lewat Tinker

```powershell
php artisan tinker
```

Di dalam Tinker (satu baris satu perintah):

```php
$search = app(\App\Modules\Article\Application\UseCases\SearchArticles::class);
$search->execute();
$detail = app(\App\Modules\Article\Application\UseCases\GetPublishedArticle::class);
$detail->execute('artikel-yang-belum-ada');
exit
```

Jika belum ada artikel `published`, pencarian menghasilkan `items: []`, `total: 0`, dan detail untuk slug tak dikenal menghasilkan `null`.

## 4. Prinsip implementasi

- Controller (tahap S1-04.5D) akan menangani validasi request HTTP dan render Inertia.
- Use Case menormalisasi page, search dan category, lalu memanggil kontrak `ArticleRepository`.
- Filtering `publication_status = published` tetap dilakukan oleh implementasi repository S1-04.5B.
- Data artikel fiktif tidak ditambahkan ke database.

**Jangan commit dulu jika sedang mengikuti workflow tahap demi tahap; lakukan checkpoint setelah integrasi antarmuka sesuai rencana proyek.**
