# MENTORA — Sprint S2-01F.3
## Undangan Akun Internal Editor, Verifier, dan Admin oleh SuperAdmin

**Baseline yang dikonfirmasi pengguna:** Laravel 13.42, PHP 8.4, Vue 3, Inertia, Tailwind CSS 4, PostgreSQL 17, sistem Identity RBAC S2-01A–F.2, **149 tes lulus**.

### Ruang lingkup

Paket ini menambahkan **halaman manajemen pengguna internal** di `/superadmin/users`, bukan seluruh Dashboard SuperAdmin. URL tersebut hanya bisa diakses oleh pengguna terautentikasi, sudah verifikasi email, memiliki role `superadmin` **dan** permission `roles.assign`.

- Membuat akun internal **baru** dengan satu role `editor`, `verifier`, atau `admin`.
- Menolak promosi akun yang sudah ada, termasuk Member, Guardian, Admin, atau SuperAdmin.
- Menolak `superadmin` sebagai role undangan, meski dikirim lewat HTTP langsung.
- Menyimpan `assigned_by` di `user_roles`, dan audit event tanpa token/password di `staff_invitation_events`.
- Password awal adalah hash dari nilai acak yang tidak ditampilkan dan tidak diketahui pengelola. Staf menetapkan password lewat link reset password Laravel.
- Setelah menentukan password, staf login dan menyelesaikan **verifikasi email** sebelum membuka rute khusus role masing-masing.
- Jika pengiriman email gagal, akun tetap terbuat; SuperAdmin bisa mengirim ulang dari UI. Pengiriman ulang dibatasi (cooldown 60 detik, throttle HTTP).
- Daftar pengguna dipaginasi 12 per halaman; pencarian di UI hanya pada halaman tersebut.
- Tidak menimpa `Register.vue`, `CreateNewUser.php`, `routes/web.php`, `resources/js/app.ts`, Login, akun SuperAdmin pertama, maupun Dashboard Member.

### Isi ZIP

1. `database/migrations/2026_11_26_000002_create_identity_staff_invitation_events_table.php`
2. `app/Modules/Identity/Application/Services/StaffInvitationService.php`
3. `app/Modules/Identity/Presentation/Http/Controllers/StaffInvitationsController.php`
4. `routes/mentora-superadmin-staff.php`
5. `resources/js/pages/Mentora/SuperAdmin/Users.vue`
6. `tests/Feature/Modules/Identity/StaffInvitationsTest.php`
7. `README_S2-01F3_Staff_Invitations.md`

### Instalasi di Windows / PowerShell

Jalankan dari root proyek:

```powershell
Set-Location "D:\Project Website\MENTORA"

Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Staff_Invitations_S2-01F3.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

**Satu baris di `routes/web.php`** (letakkan di luar `if (app()->environment('local'))`, sejajar dengan `require __DIR__ . '/mentora-member-dashboard.php';`):

```php
require __DIR__ . '/mentora-superadmin-staff.php';
```

**Satu pengecualian di `resources/js/app.ts`**. Cari kondisi yang mencegah halaman `Mentora/Member/Dashboard` dibungkus layout Laravel. Tambahkan halaman berikut ke daftar yang sama:

```ts
'Mentora/SuperAdmin/Users'
```

Jika pengecualian berupa kondisi boolean, gunakan:

```ts
|| name === 'Mentora/SuperAdmin/Users'
```

**Jangan ganti seluruh `app.ts` atau menambahkan wrapper kedua.** Halaman `Users.vue` sudah memiliki layout sidebar MENTORA sendiri.

Setelah itu:

```powershell
php artisan migrate
php artisan optimize:clear
php artisan route:list --path=superadmin
php artisan test tests/Feature/Modules/Identity/StaffInvitationsTest.php
php artisan test
npm run build
git status -sb
```

**Target pengujian:** 17 tes baru, total **166 tes** jika seluruh baseline 149 tes masih tetap dan tidak ada tes lain ditambahkan. Sintaks PHP sudah diuji secara statis dalam pembuatan ZIP; pengujian integrasi hanya dapat dipastikan di lingkungan Laravel pengguna. Kirim pesan error lengkap jika hasil berbeda.

### Uji manual

1. Login sebagai SuperAdmin **terverifikasi** yang sudah dibuat pada S2-01F.1.
2. Buka `http://127.0.0.1:8000/superadmin/users`.
3. Isi form undangan dengan **email baru** yang Anda kuasai, misal akun Editor. Jangan gunakan email Member/Guardian yang sudah terdaftar.
4. Pilih Editor, Verifier, atau Admin; submit satu kali.
5. Jika `.env` berisi `MAIL_MAILER=log`, buka `storage/logs/laravel.log` dan cari **tautan Reset Password paling baru untuk email staf yang diundang**. Jangan buka link untuk akun lain, dan jangan bagikan token.
6. Dengan **sesi browser terpisah/Incognito**, staf menggunakan link tersebut untuk menentukan password, login, membuka `/email/verify`, mengklik **Resend verification email**, lalu membuka link verifikasi terbaru untuk akun staf itu. Staf tidak boleh bisa membuka `/superadmin/users` (HTTP 403).
7. Untuk link kedaluwarsa/pesan gagal, kembali sebagai SuperAdmin dan gunakan tombol **Kirim Ulang** (setelah 60 detik). Jangan mengundang email yang sama ulang.

### Tentang pengiriman email

`MAIL_MAILER=log` menyimpan isi notifikasi di `storage/logs/laravel.log`, bukan mengirimkannya melalui Gmail. Mode ini hanya cocok untuk pengembangan. Ubah ke SMTP/provider mail yang sah dan diuji sebelum production. **Jangan pernah menyimpan secret SMTP di Git**.

### Pembatasan keamanan

- Hanya rute `GET /superadmin/users`, `POST /superadmin/staff-invitations`, dan `POST /superadmin/staff-invitations/{user}/resend` yang ditambahkan.
- Backend memvalidasi role terhadap daftar tetap dan memeriksa ulang otorisasi di lapisan service.
- Undangan bukan fitur massal, dan tidak bisa mengubah role akun yang sudah ada.
- Pengguna internal belum memperoleh Dashboard Editor/Verifier/Admin khusus. Setelah verifikasi mereka sementara menggunakan `/dashboard` umum; fitur role khusus dikerjakan pada sprint berikutnya.
- Pengelolaan role pengguna lama (pemberian/revokasi) **belum** disediakan. Jangan menambahkan query edit role sendiri sebelum fitur kontrol + audit dirancang.
- S2-01F.4 akan mengarahkan `/dashboard` ke tujuan role yang tersedia, tanpa merusak rute publik.

### Troubleshooting

- **404** `/superadmin/users`: `require` di `routes/web.php` belum dipasang atau route cache belum dibersihkan.
- **403**: akun tidak memiliki `superadmin` dan `roles.assign`; SuperAdmin tidak dapat mempromosikan akun lain melalui registrasi biasa.
- **Redirect email/verify**: email akun belum terverifikasi.
- **Halaman bertumpuk dengan sidebar Laravel**: pengecualian `Mentora/SuperAdmin/Users` belum ditambah di `app.ts`.
- **Akun dibuat namun notifikasi gagal**: lihat peringatan di UI, periksa konfigurasi mailer, gunakan Kirim Ulang setelah jeda.
- **npm run build gagal**: periksa output TypeScript/Vue dan kirimkan log lengkap untuk patch; jangan hapus konfigurasi proyek yang sudah bekerja.

**Tidak diperlukan** menjalankan bootstrap SuperAdmin lagi, menjalankan seeder yang mengubah role pengguna, atau menghapus akun lama.
