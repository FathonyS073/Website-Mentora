# MENTORA — S2-01D: Middleware & Gate Authorization (Laravel 13.42)

## Cakupan paket

Paket berisi empat file:

1. `app/Modules/Identity/Presentation/Http/Middleware/RequireRole.php` — guard `rbac.role:<role>`.
2. `app/Modules/Identity/Presentation/Http/Middleware/RequirePermission.php` — guard `rbac.permission:<permission>`.
3. `app/Modules/Identity/RbacServiceProvider.php` — **pembaruan** provider S2-01C: binding lama dipertahankan; ditambahkan alias middleware dan 18 Gate dari permission matrix.
4. `tests/Feature/Modules/Identity/RbacMiddlewareTest.php` — **14 pengujian PHPUnit**, mendaftarkan route uji secara dinamis tanpa route baru pada sistem produksi.

**Tidak menyentuh `routes/web.php`, `bootstrap/app.php`, `bootstrap/providers.php`, Login, Register, Dashboard, model User, migration, seeder, atau frontend.**

### Mekanisme keamanan

- Semua middleware menggunakan `RbacAccessReader` yang sudah terpasang pada S2-01C. Tidak ada role default atau SuperAdmin bypass.
- Pengguna anonim ditolak sebagai *unauthenticated* (redirect login untuk request HTML, 401 untuk JSON).
- Pengguna terautentikasi tanpa izin mendapat 403, termasuk jika role belum ditetapkan atau kode tidak dikenal.
- Pencabutan role dari `user_roles` segera tercermin pada request berikutnya, karena tidak ada cache role antarrequest.
- Gate (`Gate::allows('profile.view')`, `can:profile.view`) juga didaftarkan dari matriks 18 izin, tanpa `Gate::before` yang memberikan izin universal.
- **Guard role/permission bukan pengganti policy sumber daya.** Pemeriksaan kepemilikan data, hubungan Guardian–Member beserta persetujuan, dan aturan transisi draft/verifikasi/publikasi harus ditambahkan di modul fungsional terkait, bukan diasumsikan aman karena role.
- Route dashboard saat ini **belum** ditempeli middleware role karena tidak ada akun yang telah diprovision. Ini sengaja untuk menjaga login dan pengujian yang telah disetujui; S2-01E akan mengatur provisioning role, baru S2-02+ memasang guard pada dashboard baru.

## Langkah 1 — Ekstrak paket

```powershell
Set-Location "D:\Project Website\MENTORA"

Copy-Item "app\Modules\Identity\RbacServiceProvider.php" "$env:TEMP\MENTORA_RbacServiceProvider_S2-01C_Backup.php"

Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Identity_RBAC_Middleware_S2-01D.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

**Tidak perlu** menjalankan migration / seeder lagi. Jangan melakukan `migrate:fresh`.

## Langkah 2 — Refresh cache dan tes

```powershell
php artisan optimize:clear
php artisan test tests/Feature/Modules/Identity/RbacMiddlewareTest.php
php artisan test
```

Target dari baseline **94 tes** adalah **14 tes tambahan = 108 tes** jika tidak ada tes lain yang ditambah.
Tidak perlu `npm run build`, karena paket ini tidak mengubah Vue atau aset.

## Langkah 3 — Inspect status Git

```powershell
git status --short
git diff --check
```

Jangan commit sampai hasil tes benar.

## Contoh penggunaan pada route dashboard BARU (jangan terapkan pada `/dashboard` yang lama sekarang)

```php
Route::middleware(['auth', 'verified', 'rbac.role:editor'])
    ->get('/editor/dashboard', EditorDashboardController::class);

Route::middleware(['auth', 'verified', 'rbac.permission:content.review'])
    ->get('/verifier/review', ReviewController::class);

// Penggunaan Gate bawaan Laravel:
Route::middleware(['auth', 'verified', 'can:analytics.view'])
    ->get('/admin/analytics', AnalyticsController::class);
```

Penggunaan Gate pada server:

```php
if (Gate::allows('tracker.manage')) {
    // Masih harus memeriksa kepemilikan tracker yang akan dibaca/diubah.
}
```

**Route ini contoh saja.** Tidak ada kelas controller baru tersebut pada paket, jangan ditempel ke `routes/web.php`.

## Jika tes gagal

Kirim keluaran terminal untuk tes khusus dulu. Jangan menambah alias manual di `bootstrap/app.php` atau mengganti seluruh `routes/web.php`. Pastikan `RbacServiceProvider` tetap didaftarkan **sekali** di `bootstrap/providers.php` seperti S2-01C.

## Tahap berikutnya

**S2-01E — Provisioning Member saat registrasi dan penetapan role administratif melalui alur terotorisasi**, dengan audit jejak perubahan. Jangan memberikan role Admin/SuperAdmin melalui form registrasi publik.
