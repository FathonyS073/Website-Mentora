# MENTORA — S2-01F.1: Bootstrap Akun SuperAdmin Pertama

Target: Laravel 13.42 + PostgreSQL + RBAC S2-01B/C/D/E. Paket ini **menambahkan** file; tidak mengubah Login/Register, migrasi, Dashboard Member, atau route yang sudah ada.

## Desain keamanan

- Perintah hanya dijalankan melalui Artisan, **tidak tersedia sebagai HTTP route**.
- **Hanya untuk akun SuperAdmin pertama.** Jika ada satu penetapan `superadmin`, perintah menolak pembuatan berikutnya.
- **Tidak mengambil alih akun Member lama.** Gunakan alamat email baru khusus SuperAdmin yang belum ada di tabel `users`.
- Default adalah **dry-run**. Perlu `--apply` dan `--confirm` yang sama persis dengan alamat email.
- Dalam `APP_ENV=production` perlu `--allow-production` dan mailer sungguhan (bukan `log` / `array`).
- Password awal dibuat acak, di-hash, dan **tidak pernah diberikan atau dicetak**. Pemilik akun mengatur password dengan tautan reset yang dikirim Laravel.
- Email tetap belum diverifikasi. Setelah mengatur password, login dan verifikasi email melalui route standar.
- Tidak memicu event `Registered` sehingga listener registrasi publik tidak menambahkan role `member` ke SuperAdmin.
- Penugasan role dicatat di `user_roles` dengan `assigned_by=null` karena ini proses bootstrap pertama.
- Jika pengiriman notifikasi gagal setelah akun dibuat, akun **tetap tersimpan**. Jangan jalankan bootstrap lagi; gunakan fitur **Lupa Kata Sandi** dan **Kirim Ulang Verifikasi** setelah mailer diperbaiki.

## 1. Instalasi (PowerShell)

```powershell
Set-Location "D:\Project Website\MENTORA"

Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_First_SuperAdmin_Bootstrap_S2-01F1.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

Pada `bootstrap/providers.php`, **tambahkan** berikut di dalam array provider. Jangan menghapus provider yang sudah ada:

```php
App\Modules\Identity\SuperAdminBootstrapServiceProvider::class,
```

Pastikan `RbacServiceProvider` dan `RegistrationRoleServiceProvider` tetap terdaftar.

## 2. Periksa dan uji (belum membuat akun)

```powershell
php artisan optimize:clear
php artisan list --raw | Select-String "mentora:rbac:bootstrap-superadmin"
php artisan test tests/Feature/Modules/Identity/FirstSuperAdminBootstrapTest.php
php artisan test
```

Target tambahan: **10 PHPUnit tests**. Pengujian memakai database testing dengan `RefreshDatabase` dan akun `example.test`, tidak membuat SuperAdmin nyata pada database aplikasi normal.

## 3. Jalankan pratinjau (tidak mengubah database)

Pilih email yang Anda kuasai dan **belum pernah didaftarkan di MENTORA**. Contoh ini adalah placeholder, **bukan akun sungguhan**.

```powershell
php artisan mentora:rbac:bootstrap-superadmin `
  --name="Pemilik MENTORA" `
  --email="alamat-baru@domain-anda.com"
```

Target: `PRATINJAU SAJA` dan exit code 0.

## 4. Buat akun pertama (hanya jika pratinjau benar)

```powershell
php artisan mentora:rbac:bootstrap-superadmin `
  --name="Pemilik MENTORA" `
  --email="alamat-baru@domain-anda.com" `
  --apply `
  --confirm="alamat-baru@domain-anda.com"
```

`--confirm` harus **persis** dengan `--email`. Bila jalankan di production, konfigurasi mail harus benar dan tambahkan `--allow-production`.

**Jangan gunakan email Member yang sudah terdaftar; buat akun SuperAdmin khusus dan terpisah.**

## 5. Aktivasi akun (MAIL_MAILER=log pada local)

Karena mailer lokal Anda masih `log`, Laravel menyimpan **tautan reset password dan verifikasi email** di:

```
D:\Project Website\MENTORA\storage\logs\laravel.log
```

1. Temukan **tautan reset password paling baru** untuk email SuperAdmin baru; jangan gunakan tautan milik akun Member sebelumnya. Buka di browser dan tentukan kata sandi baru yang kuat.
2. Login dengan akun SuperAdmin baru.
3. Buka `/email/verify`, lalu klik tautan verifikasi email terbaru untuk akun itu. Link yang lama mungkin sudah kedaluwarsa; kirim ulang bila diperlukan.
4. Setelah login, `/dashboard` saat ini mungkin masih menampilkan halaman lama karena dashboard SuperAdmin belum dibuat. Itu wajar; akses istimewa tidak berarti dashboard otomatis tersedia.

**Jangan kirim log atau URL reset/verifikasi lengkap ke orang lain.** File log mengandung token yang sensitif. Jangan commit `storage/logs` atau `.env` ke Git.

## 6. Verifikasi role tanpa membocorkan data akun

Masuk `php artisan tinker`, lalu:

```php
\Illuminate\Support\Facades\DB::table('user_roles')->where('role_code','superadmin')->count();
```

Target setelah `--apply`: `1`. Perintah bootstrap berikutnya harus **ditolak**, bahkan bila email lain digunakan.

## 7. Troubleshooting

- `Role superadmin belum tersedia` atau izin belum lengkap: jalankan `php artisan db:seed --class=RbacPermissionSeeder`, lalu ulangi pratinjau.
- `Email sudah digunakan`: gunakan email **baru**, jangan mempromosikan Member lama secara diam-diam.
- `Sudah ada SuperAdmin`: jangan membuat ulang; pulihkan akses melalui `/forgot-password` dan verifikasi email.
- `MAIL_MAILER=log`: email tidak masuk Gmail; tautannya hanya tersimpan dalam log. Saat produksi gunakan mailer sesungguhnya.
- `Class ... not found`: pastikan ZIP sudah terekstrak di root proyek, provider terdaftar, dan `php artisan optimize:clear` berhasil.

## Perubahan berikutnya

S2-01F.2: Registrasi Member/Guardian. S2-01F.3: Undangan akun internal oleh SuperAdmin. S2-01F.4: Redirect dashboard berdasarkan role. Belum ada form HTTP yang dapat memberikan `superadmin`.
