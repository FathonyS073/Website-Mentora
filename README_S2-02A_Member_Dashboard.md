# MENTORA — S2-02A: Dashboard Member (fondasi UI & routing aman)

Stack: Laravel 13.42.0, Vue 3 + Inertia, Tailwind 4, PostgreSQL 17.

## 1. Isi paket

- `app/Modules/Identity/Presentation/Http/Controllers/MemberDashboardController.php`
- `routes/mentora-member-dashboard.php`
- `resources/js/pages/Mentora/Member/Dashboard.vue`
- `tests/Feature/Modules/Identity/MemberDashboardTest.php`

Semua ini **file baru**. Paket tidak menimpa route `/dashboard` lama, halaman publik, Login, Register, maupun modul RBAC.

## 2. Instalasi

Buka PowerShell:

```powershell
Set-Location "D:\Project Website\MENTORA"
Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Member_Dashboard_S2-02A.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

## 3. Satu baris di `routes/web.php`

Tambahkan **satu kali** di **luar** blok `if (app()->environment('local'))`:

```php
require __DIR__ . '/mentora-member-dashboard.php';
```

File baru tersebut memakai middleware `auth`, `verified`, `rbac.role:member`, dan `rbac.permission:profile.view`. Alias `rbac.*` telah didaftarkan di S2-01D. Jangan menghapus route `/dashboard` lama.

## 4. Satu kondisi di `resources/js/app.ts`

Cari pengecualian terhadap `AppLayout` yang sebelumnya Anda tambahkan untuk halaman `Mentora/About`, `Mentora/Articles/Index`, dsb. Tambahkan **satu kondisi** di tempat yang sama:

```typescript
|| name === 'Mentora/Member/Dashboard'
```

Jika daftar yang sudah ada menggunakan `Array.includes`, tambahkan string `'Mentora/Member/Dashboard'` ke array itu saja. Halaman Member memakai **layout dashboard miliknya sendiri**, tidak boleh dibungkus `AppLayout` sekali lagi.

## 5. Pengujian

```powershell
php artisan route:clear
php artisan route:list --path=member
php artisan test tests/Feature/Modules/Identity/MemberDashboardTest.php
php artisan test
npm run build
```

Target bila baseline 118 tests tidak berubah: 9 tes baru -> **127 tes total**. Tes menguji guest redirect, Member yang sudah verifikasi, user tanpa role, Editor/Admin/SuperAdmin, Member belum verifikasi, pencabutan role, dan route `/dashboard` lama.

## 6. Buka halaman

`http://127.0.0.1:8000/member/dashboard`

Halaman hanya terbuka bagi pengguna dengan role `member` yang sudah memverifikasi email. Jika Anda mencoba membuka sebagai guest -> login. Jika akun lama tidak memiliki Member -> 403. Jika belum verifikasi email -> halaman verifikasi.

**Tidak perlu memberi role Admin/SuperAdmin pada akun untuk menguji dashboard Member.** Akun baru melalui `/register` otomatis memperoleh role Member sesuai S2-01E. Jika ingin menguji dengan akun lama, tentukan peran akun tersebut melalui mekanisme yang berwenang; jangan mengubah database langsung tanpa pemeriksaan.

## 7. Batasan tahap ini

- Struktur visual mengacu pada referensi Dashboard Member MENTORA yang disetujui sebelumnya.
- Tidak ada grafik, angka progress, rasio profil, tanggal deadline, atau rekomendasi fiktif. Semuanya bertanda "Dalam pengembangan" sampai sumber data Tracker/Matching/Onboarding benar-benar tersedia.
- Navigasi hanya mengaktifkan URL yang sudah tersedia. Menu fitur selanjutnya belum dapat diklik.
- Route `/dashboard` lama **belum** diarahkan otomatis. Integrasi redirect login berbasis role akan dilakukan setelah dashboard role lain tersedia.
- Tidak menambah migration, seeder, atau perubahan autentikasi.

## Jika error

- Error `403`: akun tidak mempunyai role `member` — periksa assignment tanpa menambah hak istimewa.
- Error `404`: baris `require` belum ditambahkan atau route cache belum dibersihkan.
- Error `AppLayout` ganda: tambahkan pengecualian page `Mentora/Member/Dashboard` di `app.ts`.
- `npm run build` gagal: kirim pesan error lengkap; proyek lokal Anda merupakan sumber kebenaran untuk konfigurasi frontend.
