# MENTORA — Patch S2-01F.1: PHPUnit Fatal Error

## Kesalahan yang diperbaiki

`FirstSuperAdminBootstrapTest::options()` dideklarasikan private dan berbenturan dengan metode public `options()` dari `Illuminate\Foundation\Testing\TestCase`. Mengganti helper internal menjadi `bootstrapOptions()` di seluruh file tes. **Tidak ada perubahan kode produksi, schema, konfigurasi, atau akun.**

## Instalasi (PowerShell, lokasi proyek D:\Project Website\MENTORA)

```powershell
Set-Location "D:\Project Website\MENTORA"
Expand-Archive -Path "$env:USERPROFILE\Downloads\MENTORA_S2-01F1_PHPUnit_Fix.zip" -DestinationPath "D:\Project Website\MENTORA" -Force
php artisan test tests/Feature/Modules/Identity/FirstSuperAdminBootstrapTest.php
php artisan test
```

Diharapkan tes spesifik lulus 10 tes. Hitungan total mengacu kepada paket lain yang telah terpasang; bila S2-02A 9 tes sudah terpasang, baseline 127 + 10 = 137 tes.

## Status akun SuperAdmin

Dari terminal pengguna, perintah bootstrap `--apply` sudah **berhasil membuat satu akun SuperAdmin**. **Jangan jalankan --apply kembali.** Kode patch ini hanya tes.

Karena `MAIL_MAILER=log`, tautan reset password dan verifikasi email tersimpan di `storage/logs/laravel.log`, bukan di Gmail. Gunakan tautan reset password terbaru, set password, login, dan verifikasi email; jangan bagikan tautan/token.
