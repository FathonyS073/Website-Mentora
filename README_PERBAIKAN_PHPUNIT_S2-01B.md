# Perbaikan test PHPUnit — S2-01B Identity RBAC

Masalah: `Call to undefined function uses()` disebabkan oleh file `RbacSchemaTest.php` lama yang ditulis memakai Pest, sementara proyek MENTORA memakai PHPUnit.

Paket ini **hanya mengganti satu file pengujian**:
`tests/Feature/Modules/Identity/RbacSchemaTest.php`.

**Tidak menjalankan migrasi ulang**, tidak mengubah `users`, autentikasi, seed, atau tabel RBAC yang telah berhasil dibuat.

## Pemasangan

Di PowerShell, folder proyek: `D:\Project Website\MENTORA`

```powershell
Set-Location "D:\Project Website\MENTORA"
Copy-Item "tests\Feature\Modules\Identity\RbacSchemaTest.php" "$env:TEMP\MENTORA_RbacSchemaTest_Pest_Backup.php"
Expand-Archive -Path "$env:USERPROFILE\Downloads\MENTORA_RBAC_PHPUnit_Fix_S2-01B.zip" -DestinationPath "D:\Project Website\MENTORA" -Force
php artisan test tests/Feature/Modules/Identity/RbacSchemaTest.php
php artisan test
```

Target (jika tidak ada pengujian lain yang ditambahkan): test RBAC 5 passed dan keseluruhan 83 passed (sebelumnya 78). Jika target berbeda, lihat output aktual.

## Bila perlu mengembalikan file semula

```powershell
Copy-Item "$env:TEMP\MENTORA_RbacSchemaTest_Pest_Backup.php" "tests\Feature\Modules\Identity\RbacSchemaTest.php" -Force
```
