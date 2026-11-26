# MENTORA — S2-01F.2: Registrasi Publik Member dan Guardian

**Target stack:** Laravel 13.42, PHP 8.4, Vue 3, Inertia, Tailwind 4, PostgreSQL 17. Berjalan di atas RBAC S2-01B/C/D/E dan bootstrap SuperAdmin S2-01F.1. Paket ini **tidak** membuat atau memodifikasi akun SuperAdmin lama.

## Fitur

- Halaman `/register` tetap memakai desain putih–indigo MENTORA yang sebelumnya disetujui, ditambah dua kartu pilihan: **Pelajar/Mahasiswa (Member)** dan **Orang Tua/Wali (Guardian)**.
- Browser lama yang tidak mengirim `account_type` tetap mendaftarkan **Member** (kompatibilitas dengan test dan klien registrasi lama).
- Backend memvalidasi `account_type` **hanya** `member` / `guardian`; nilai lain termasuk `admin`, `editor`, `verifier`, dan `superadmin` ditolak.
- Field manipulatif `role`, `role_code`, `roles`, `is_admin`, `email_verified_at` tidak dipakai untuk menetapkan role. Verifikasi email tetap wajib.
- Pembuatan user dan penetapan **tepat satu role** dilakukan dalam **satu transaksi database**. Jika penetapan role gagal, pembuatan user di-rollback.
- Listener `AssignMemberOnRegistration` dari S2-01E tetap terpasang dan **tidak perlu diedit**: ia hanya menambahkan Member bila akun masih belum mempunyai role. Guardian sudah mempunyai role ketika event `Registered` diterbitkan, sehingga tidak mendapatkan Member tambahan.
- Guardian **tidak** otomatis boleh melihat data anak/Member lain. Fitur hubungan pendampingan berbasis persetujuan dikerjakan pada tahap berikutnya.
- Tidak ada migration baru, route baru, provider baru, atau perubahan permission matrix.

## Isi ZIP (relative project root)

```text
app/Actions/Fortify/CreateNewUser.php
app/Modules/Identity/Application/Services/PublicRegistrationRoleAssigner.php
resources/js/pages/auth/Register.vue
tests/Feature/Modules/Identity/PublicRegistrationRolesTest.php
README_S2-01F2_Registrasi_Publik.md
```

## 1. Backup sebelum instalasi (PowerShell)

Jalankan dari direktori proyek. Backup **dua file yang akan diganti**:

```powershell
Set-Location "D:\Project Website\MENTORA"
Copy-Item "app\Actions\Fortify\CreateNewUser.php" "$env:TEMP\MENTORA_CreateNewUser_pre_S2-01F2.php" -Force
Copy-Item "resources\js\pages\auth\Register.vue" "$env:TEMP\MENTORA_Register_pre_S2-01F2.vue" -Force
```

**Penting:** jika `CreateNewUser.php` atau `Register.vue` sudah Anda ubah sendiri setelah S1-Auth, bandingkan dulu dengan paket ini sebelum menimpa. File CreateNewUser dalam paket dibuat berdasarkan struktur `profileRules()` + `passwordRules()` dan password hashed cast yang ada pada repository MENTORA.

## 2. Instal ZIP

```powershell
Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Public_Registration_Member_Guardian_S2-01F2.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
php artisan optimize:clear
```

**Tidak perlu** menambah provider, mengubah `web.php`, membuat tabel baru, atau mem-bootstrap SuperAdmin ulang.

## 3. Tes backend dan build frontend

```powershell
php artisan test tests/Feature/Modules/Identity/PublicRegistrationRolesTest.php
php artisan test
npm run build
git status -sb
```

Target tes baru: **12 passed**. Jika baseline aplikasi sesudah S2-01F.1 dan S2-02A adalah **137 passed**, target total **149 passed**. Angka total bergantung pada patch dan file lain yang sudah Anda pasang. Jika PHP lint sukses tetapi tes gagal, **kirim error tes sebelum melanjutkan**, jangan ubah DB atau migrasi secara acak.

## 4. Uji browser

1. Pastikan `/register` menampilkan pilihan **Pelajar/Mahasiswa** dan **Orang Tua/Wali**.
2. **Logout** akun SuperAdmin terlebih dahulu atau gunakan jendela **Incognito/Private**. Gunakan **email uji baru** untuk membuat akun Guardian; akun SuperAdmin tidak digunakan untuk uji registrasi publik.
3. Karena `MAIL_MAILER=log`, klik "Resend verification email" bila perlu dan ambil tautan terbarunya dari `storage/logs/laravel.log`. Tautan bersifat rahasia.
4. Setelah verifikasi, Guardian masuk `/dashboard` sementara (dashboard khusus Guardian belum dibuat). Jangan mengarahkan Guardian ke `/member/dashboard`.
5. Uji akun Member baru: setelah verifikasi, `/dashboard` diarahkan ke `/member/dashboard` sesuai redirect yang sudah diperbaiki.

## 5. Role dan keamanan

| Jenis akun | Daftar sendiri | Mekanisme |
|--|--|--|
| Guest | Tidak perlu akun | Halaman publik |
| Member | Ya | `/register` → Member |
| Guardian | Ya | `/register` → Guardian |
| Editor, Verifier, Admin | Tidak | Undangan/penugasan SuperAdmin tahap S2-01F.3 |
| SuperAdmin | Tidak | S2-01F.1 bootstrap pertama melalui Artisan; sudah dilakukan |

Guardian belum mempunyai dashboard dan relasi anggota keluarga. Perizinan `guardian.progress.view` hanya permission RBAC, **bukan otorisasi untuk membaca data Member lain secara otomatis**. Pemilik data harus memberi persetujuan melalui aturan domain yang akan dibangun kelak.

## 6. Rollback (bila diperlukan)

```powershell
Copy-Item "$env:TEMP\MENTORA_CreateNewUser_pre_S2-01F2.php" "app\Actions\Fortify\CreateNewUser.php" -Force
Copy-Item "$env:TEMP\MENTORA_Register_pre_S2-01F2.vue" "resources\js\pages\auth\Register.vue" -Force
php artisan optimize:clear
npm run build
```

File baru `PublicRegistrationRoleAssigner.php` dan tesnya boleh dibiarkan; setelah rollback tidak terhubung ke Fortify.

## Tahap selanjutnya

- **S2-01F.3** — Undangan dan aktivasi akun Editor, Verifier, Admin oleh SuperAdmin.
- **S2-01F.4** — Dashboard role dispatcher/pengalihan terpusat untuk peran yang tersedia.
- **S2-02B** — Onboarding 4 langkah profil pendidikan Member.
