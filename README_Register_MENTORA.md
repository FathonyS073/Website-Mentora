# MENTORA — Redesign Halaman Register

Paket ini dibuat untuk proyek Laravel 13 + Vue/Inertia + Wayfinder MENTORA yang **sudah memasang paket redesign Login sebelumnya**.

## Isi paket

1. `resources/js/pages/auth/Register.vue` — mengganti UI halaman pendaftaran.
2. `resources/js/layouts/auth/AuthSimpleLayout.vue` — mengaktifkan desain MENTORA untuk Register, sambil mempertahankan desain Login dan fallback untuk halaman autentikasi lain.

Tidak ada perubahan pada API registrasi, Fortify, middleware, `routes/web.php`, database, atau `app.ts`.

## 1. Backup (PowerShell)

Jalankan di `D:\Project Website\MENTORA`:

```powershell
Set-Location "D:\Project Website\MENTORA"
Copy-Item "resources\js\pages\auth\Register.vue" "$env:TEMP\MENTORA_Register_Backup.vue" -Force
Copy-Item "resources\js\layouts\auth\AuthSimpleLayout.vue" "$env:TEMP\MENTORA_AuthLayout_PreRegister_Backup.vue" -Force
```

## 2. Ekstrak

Setelah unduh ZIP ke folder Downloads:

```powershell
Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Register_Redesign_S1-Auth.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

## 3. Pengujian

```powershell
php artisan test tests/Feature/Auth/RegistrationTest.php
php artisan test tests/Feature/Auth/AuthenticationTest.php
php artisan test
npm run build
```

Buka `/register`, uji isian nama, email, password, konfirmasi password, tautan Masuk, dan error validasi. Lalu cek ulang `/login` untuk memastikan desain lama tetap utuh.

### Catatan tentang persetujuan legal

Referensi mockup pendaftaran MENTORA memiliki checkbox Syarat & Ketentuan serta Kebijakan Privasi. Paket ini **tidak** membuat checkbox legal semu atau tautan 404: halaman ketentuan/privasi dan validasi persetujuan backend harus disiapkan sebelum checkbox itu diterapkan secara resmi.

### Pemulihan jika perlu

```powershell
Copy-Item "$env:TEMP\MENTORA_Register_Backup.vue" "resources\js\pages\auth\Register.vue" -Force
Copy-Item "$env:TEMP\MENTORA_AuthLayout_PreRegister_Backup.vue" "resources\js\layouts\auth\AuthSimpleLayout.vue" -Force
```

## Keamanan dan kompatibilitas

`Register.vue` menggunakan `store.form()` dari `@/routes/register` serta nama field `name`, `email`, `password`, dan `password_confirmation` tanpa perubahan. Validasi server tetap di Laravel/Fortify. `PasswordInput` bawaan dipakai agar tombol tampil/sembunyi kata sandi tetap berfungsi. Kode mengikuti file komponen Login yang telah dipakai pada tahap sebelumnya; perubahan proyek lokal setelah tahap itu harus ditinjau sebelum overwrite.
