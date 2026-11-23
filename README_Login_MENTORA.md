# Perbaikan UI Login MENTORA

Paket ini menyesuaikan tampilan login Laravel Vue Starter Kit dengan desain publik MENTORA. Backend, database, route login, dan perilaku passkey tidak diganti.

## File yang diganti (hanya dua)

1. `resources/js/pages/auth/Login.vue` — formulir login berbahasa Indonesia, email/password, checkbox ingat saya, reset password, pendaftaran, tombol passkey bawaan, status dan error.
2. `resources/js/layouts/auth/AuthSimpleLayout.vue` — logo MENTORA, kartu putih, latar lembut biru, responsif. **Layout MENTORA diterapkan hanya untuk halaman login; halaman register, reset password, dan 2FA mempertahankan tampilan bawaan.**

## Sebelum memasang

> Penting: Paket menggunakan struktur resmi Laravel Vue Starter Kit (Inertia + Fortify) dan komponen MENTORA yang sudah ada. Jika `Login.vue` Anda telah dikustomisasi untuk login sosial, SSO, CAPTCHA, atau logika khusus lain, bandingkan perubahan dengan file asli sebelum menimpa agar fitur khusus tidak hilang.

```powershell
Set-Location "D:\Project Website\MENTORA"

Copy-Item "resources\js\pages\auth\Login.vue" "$env:TEMP\MENTORA_Login_SebelumRedesign.vue"
Copy-Item "resources\js\layouts\auth\AuthSimpleLayout.vue" "$env:TEMP\MENTORA_AuthSimpleLayout_SebelumRedesign.vue"

Expand-Archive `
    -Path "$env:USERPROFILE\Downloads\MENTORA_Login_Redesign_S1-Auth.zip" `
    -DestinationPath "D:\Project Website\MENTORA" `
    -Force
```

## Pengujian

```powershell
php artisan test tests/Feature/Auth/AuthenticationTest.php
php artisan test
npm run build
```

Buka `http://127.0.0.1:8000/login`, refresh paksa `Ctrl + Shift + R`. Pastikan juga `/register` dan `/forgot-password` tetap tampil normal dan tidak memiliki layout ganda.

Cek manual: login valid, login salah, lihat/sembunyikan kata sandi, ingat saya, lupa sandi, link daftar, passkey bila sudah tersedia/dikonfigurasi.

## Pemulihan jika perlu

```powershell
Copy-Item "$env:TEMP\MENTORA_Login_SebelumRedesign.vue" "resources\js\pages\auth\Login.vue" -Force
Copy-Item "$env:TEMP\MENTORA_AuthSimpleLayout_SebelumRedesign.vue" "resources\js\layouts\auth\AuthSimpleLayout.vue" -Force
```

Tidak perlu mengubah `app.ts`, `routes/web.php`, controller, provider, atau migration.

## Catatan

Komponen yang sudah ada dan digunakan oleh paket: `MentoraBrand.vue`, `PasskeyVerify.vue`, `PasswordInput.vue`, `InputError.vue`, `TextLink.vue`, dan komponen UI standar starter kit.
