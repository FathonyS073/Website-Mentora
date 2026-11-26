# MENTORA — S2-01C: RBAC Models, Repository & Permission Matrix

## Status awal

S2-01B sudah berhasil: migration `2026_11_26_000001_create_identity_rbac_tables` berstatus Ran, tabel `roles`, `permissions`, `role_permissions`, `user_roles` tersedia, enam role tersimpan, dan **83 tests / 441 assertions** lulus setelah perbaikan PHPUnit.

## Yang ditambahkan

Paket ini **hanya berisi file baru**: 2 model Role/Permission, 1 matriks permission, 1 contract, 1 implementasi repository baca, 1 query aplikasi, 1 DTO snapshot, 1 service provider khusus RBAC, 1 seeder, dan 11 tes PHPUnit.

Tidak mengubah `App\Models\User`, `IdentityServiceProvider`, `routes/web.php`, login/register, dashboard, migration, maupun file Vue yang telah disetujui.

### Aturan keamanan

- Role `guest` tidak dibuat. Enam role internal adalah `member`, `guardian`, `editor`, `verifier`, `admin`, dan `superadmin`.
- **Default deny**: tanpa baris `user_roles`, pengguna tidak memiliki role ataupun permission, termasuk akun yang baru registrasi.
- Tidak ada **magic bypass** untuk SuperAdmin; role harus ditugaskan eksplisit dan memiliki baris di `role_permissions`.
- Editor dapat mengelola *draft* tetapi tidak memiliki izin verifikasi/publikasi. Verifier dapat memeriksa tetapi tidak mengedit draft atau langsung menerbitkan. Admin operasional tidak memiliki hak `roles.assign`/`settings.manage`.
- `RbacPermissionSeeder` menyinkronkan izin untuk **enam system role saja**; saat dijalankan ulang izin yang tidak tercantum pada matriks akan dihapus dari system role tersebut. Seeder **tidak** mengubah `user_roles` dan tidak memberi role kepada pengguna. Jalankan hanya setelah menyetujui matriks.
- Permission `guardian.progress.view` **bukan akses ke seluruh data Member**. Kebijakan berbasis hubungan dan persetujuan Member harus dilakukan saat mengakses data pada tahap S2-01D/dan modul terkait.
- Tabel RBAC memakai primary key **string `code`** untuk Role/Permission dan foreign key integer `user_id`.

## Langkah 1 — Instal paket

```powershell
Set-Location "D:\Project Website\MENTORA"

Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Identity_RBAC_Core_S2-01C.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

## Langkah 2 — Daftarkan Service Provider (SATU baris)

Buka **`bootstrap/providers.php`**. Tambahkan baris berikut **di dalam array provider**, bersama `App\Modules\Identity\IdentityServiceProvider::class` yang sudah ada:

```php
App\Modules\Identity\RbacServiceProvider::class,
```

Contoh struktur (jangan mengganti seluruh file):

```php
return [
    App\Providers\AppServiceProvider::class,
    // ... provider yang sudah ada ...
    App\Modules\Identity\IdentityServiceProvider::class,
    App\Modules\Identity\RbacServiceProvider::class,
    // ...
];
```

**Jangan menghapus** `IdentityServiceProvider`, Article, Event, atau Scholarship providers. Jika baris RBAC sudah ada, jangan didaftarkan ulang.

## Langkah 3 — Seeder matriks permission

```powershell
php artisan optimize:clear
php artisan db:seed --class=RbacPermissionSeeder
```

Hasil yang diharapkan:
- `roles`: 6
- `permissions`: 18
- `role_permissions`: 42
- `user_roles`: **tetap seperti sebelumnya**, yaitu nol jika belum ada penugasan.

Cek melalui Tinker:

```powershell
php artisan tinker
```

```php
\Illuminate\Support\Facades\DB::table('permissions')->count();
\Illuminate\Support\Facades\DB::table('role_permissions')->count();
\Illuminate\Support\Facades\DB::table('user_roles')->count();
get_class(app(\App\Modules\Identity\Domain\Contracts\RbacAccessReader::class));
exit
```

## Langkah 4 — Uji khusus RBAC lalu seluruh proyek

```powershell
php artisan test tests/Feature/Modules/Identity/RbacAccessTest.php
php artisan test
```

Target tambahan: **11 tests**, sehingga jika baseline tetap 83 tests, total menjadi **94 tests**. `npm run build` tidak wajib karena tidak ada frontend yang berubah.

## Langkah 5 — Status Git

```powershell
git status --short
git diff --check
```

Belum perlu commit hingga hasil pengujian dan matriks permission disetujui.

## Berikutnya

S2-01D membuat `Gate`/middleware & kebijakan otorisasi pada URL dashboard, termasuk tes akses negatif. **Jangan** mulai membuat atau membagikan role melalui registrasi publik pada tahap ini. S2-01E baru mengatur provisioning Member baru dan penetapan role khusus melalui jalur tepercaya.
