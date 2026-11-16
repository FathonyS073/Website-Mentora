# MENTORA — Sprint 0 / S0-05 — Fondasi Modul Identity

Target: `D:\Project Website\MENTORA`

Paket ini HANYA menambahkan 8 file baru di `app/Modules/Identity/` dan `tests/Feature/Modules/Identity/`.
Tidak ada perubahan ke `.env`, `.env.docker`, database, Laravel Fortify, `routes/web.php`, ataupun file Git.

## 1. Sebelum mengekstrak

Buat pemeriksaan/backup Git sesuai kebutuhan. Pastikan file-file tujuan dari ZIP belum ada, supaya tidak menimpa pekerjaan sebelumnya.

## 2. Ekstraksi

Ekstrak isi ZIP langsung ke root `D:\Project Website\MENTORA`. Jangan membuat folder tambahan berisi `app` di dalamnya.

## 3. Daftarkan IdentityServiceProvider SECARA MANUAL

Buka `bootstrap/providers.php`. Tambahkan satu elemen ke dalam array yang sudah ada, tanpa menghapus provider lain:

    App\Modules\Identity\IdentityServiceProvider::class,

Contoh (jika sebelumnya hanya ada satu provider):

    <?php

    return [
        App\Providers\AppServiceProvider::class,
        App\Modules\Identity\IdentityServiceProvider::class,
    ];

## 4. Periksa

PowerShell:

    Set-Location "D:\Project Website\MENTORA"
    php artisan route:clear
    php artisan route:list --name=member.account.summary -v
    php artisan test tests/Feature/Modules/Identity/AccountSummaryRouteTest.php

Harapan: route `GET|HEAD member/account/summary` memiliki middleware `web`, `auth`, dan `verified`. Pengujian menggunakan akun tiruan in-memory dan tidak menulis ke database development.

Catatan: `php artisan test` dapat membaca konfigurasi pengujian Laravel yang sudah ada; baca dulu file `phpunit.xml` jika sebelumnya telah dimodifikasi sendiri.

## Struktur layer

* Presentation: route dan controller HTTP (wajib login dan verifikasi email).
* Application: use case `GetAccountSummary`.
* Domain: `AccountReader` interface serta `AccountSummary` value object.
* Infrastructure: `EloquentAccountReader`, adapter ke model `App\Models\User` dari Laravel Fortify starter kit.
* IdentityServiceProvider: mengikat interface ke implementation dan memuat route.

## Batas penting

* Ini *vertical slice* teknis, bukan implementasi final dari onboarding, OTP 6 digit, role-permission atau dashboard.
* Jangan memindahkan `App\Models\User` atau menimpa fitur autentikasi Laravel Fortify saat Sprint 0.
* Hanya modul Identity yang didaftarkan sekarang. Modul lainnya tetap placeholder sampai use case-nya diimplementasikan.
* Jangan mengunggah `.env`, `.env.docker` atau kredensial dalam chat/Git.
