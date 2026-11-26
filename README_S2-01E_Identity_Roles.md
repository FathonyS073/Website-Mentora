# MENTORA — S2-01E Integrasi Role ke Pengguna

Target: Laravel 13.42, PHP 8.4, tabel RBAC S2-01B, permission S2-01C, middleware S2-01D.

## Cakupan dan pengaman

- **Registrasi publik selalu mendapat role `member`**, ditangani melalui event Laravel `Illuminate\Auth\Events\Registered`; input `role`, `roles`, atau `is_admin` dari browser tidak dipakai.
- Tidak ada akun yang mendapat `guardian`, `editor`, `verifier`, `admin`, atau `superadmin` melalui halaman Register.
- Pemberian role idempoten; bila akun sudah punya role apa pun, event tersebut tidak menambah atau mengubahnya.
- Tidak memodifikasi `User.php`, kelas Fortify, `routes/web.php`, database migration, `RbacServiceProvider`, atau layout Vue.
- Pengguna yang sudah ada sebelum S2-01E **tidak diubah otomatis**. Tersedia perintah backfill manual dengan **mode pratinjau sebagai default**.
- Pendaftaran dengan role Member tidak menggantikan pemeriksaan `verified` (verifikasi email) maupun pemeriksaan kepemilikan data dalam modul bisnis.

## 1. Ekstrak ZIP

```powershell
Set-Location "D:\Project Website\MENTORA"

Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Identity_Role_Assignment_S2-01E.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

Paket menambah file baru, bukan mengganti file Identity sebelumnya.

## 2. Daftarkan satu provider

Buka `bootstrap/providers.php` lalu tambahkan **satu baris di dalam array** (jika belum ada):

```php
App\Modules\Identity\RegistrationRoleServiceProvider::class,
```

Pertahankan `IdentityServiceProvider` dan `RbacServiceProvider`.

## 3. Bersihkan cache dan uji

```powershell
php artisan optimize:clear
php artisan list --raw | Select-String "mentora:rbac:backfill-members"
php artisan test tests/Feature/Modules/Identity/MemberRoleRegistrationTest.php
php artisan test
```

Target: **10 test baru**, total baseline **118** (sebelumnya 108). Jangan mengklaim sukses sebelum hasil terminal lokal keluar.

## 4. Akun yang sudah ada sebelum S2-01E (opsional)

**Tidak perlu menjalankan perintah ini jika hanya ingin menguji registrasi baru.**

Untuk meninjau jumlah semua akun lama yang belum memiliki role, tanpa mengubah data:

```powershell
php artisan mentora:rbac:backfill-members --all
```

Untuk memberikan role `member` **hanya kepada satu akun tertentu** (ID diketahui dari administrasi database; jangan mengirim data pribadi ke chat):

```powershell
php artisan mentora:rbac:backfill-members --user-id=1
php artisan mentora:rbac:backfill-members --user-id=1 --apply
```

Jika memang sudah diaudit dan semua akun tanpa role adalah anggota biasa:

```powershell
php artisan mentora:rbac:backfill-members --all --apply
```

**Penting:** `--all --apply` berdampak ke semua akun tanpa role. Tinjau daftar akun secara internal sebelum memakainya. Akun yang sudah mempunyai role apa pun tidak akan dimodifikasi.

## 5. Verifikasi data hasil registrasi baru

Setelah mendaftarkan akun percobaan melalui `/register` (di lingkungan lokal), lakukan verifikasi tanpa menampilkan email:

```powershell
php artisan tinker
```

```php
\Illuminate\Support\Facades\DB::table('user_roles')->select('role_code')->distinct()->pluck('role_code');
```

Hasilnya akan mencakup `member` bila ada akun baru yang berhasil mendaftar; keberadaan role lain dari penetapan internal sebelumnya tetap dimungkinkan.

## Berikutnya

S2-02 Dashboard Member. Sebelum dashboard dan redirect berbasis role, pastikan proses Member onboarding dan verifikasi email dijaga pada route yang semestinya. Untuk penetapan role administratif, perlu workflow terotorisasi dan audit, bukan endpoint registrasi publik.
