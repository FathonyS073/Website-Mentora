# MENTORA — Sprint 2 / S2-01B: Database Identity & RBAC

## Tujuan

Menambahkan struktur database RBAC tanpa mengubah autentikasi, `App\Models\User`, `IdentityServiceProvider`, dashboard, API, maupun komponen Vue yang sudah berjalan.

Hasil audit sebelumnya: Laravel 13.42.0, migrasi terakhir `create_articles_table` Batch 4, belum ada tabel RBAC. Struktur role mengikuti rancangan domain Identity MENTORA yang memakai kode string, **tetapi kunci `users.id` memakai integer bawaan proyek**, bukan UUID pada dokumen skema konseptual.

## Isi paket (hanya file baru)

1. `database/migrations/2026_11_26_000001_create_identity_rbac_tables.php` — membuat empat tabel: `roles`, `permissions`, `user_roles`, `role_permissions`.
2. `database/seeders/RbacCatalogSeeder.php` — mengisi enam role internal, aman dijalankan ulang.
3. `tests/Feature/Modules/Identity/RbacSchemaTest.php` — lima pengujian tabel, role, prinsip default-deny, dan uniqueness relasi.
4. `README_S2-01B_Identity_RBAC.md` — panduan ini.

Tidak ada `.env`, perubahan `bootstrap/providers.php`, migration pengguna yang lama, dan perubahan `routes/web.php`.

## 1. Pra-instalasi

Buka PowerShell di VS Code:

```powershell
Set-Location "D:\Project Website\MENTORA"
git status -sb
```

Jika sudah ada file migrasi/seeder/test dengan nama yang sama, hentikan pemasangan dan periksa dulu. Paket tidak dimaksudkan menimpa kode yang dikembangkan secara terpisah.

**Penting:** migration ini mengasumsikan `users.id` adalah `BIGINT` (Laravel starter kit). Jika Anda pernah mengubah `users.id` ke UUID, jangan jalankan migrasi ini sebelum disesuaikan.

## 2. Ekstrak ZIP

```powershell
Expand-Archive `
  -Path "$env:USERPROFILE\Downloads\MENTORA_Identity_RBAC_Database_S2-01B.zip" `
  -DestinationPath "D:\Project Website\MENTORA" `
  -Force
```

## 3. Jalankan migrasi dan seeder

```powershell
php artisan migrate
php artisan migrate:status
php artisan db:seed --class=RbacCatalogSeeder
```

Hasil: migration tercatat `Ran` dan role `member`, `guardian`, `editor`, `verifier`, `admin`, `superadmin` terisi. `Guest` tidak dibuat.

**Seeder tidak membuat akun SuperAdmin dan tidak memberikan role kepada akun yang sudah ada.** Ini penting untuk mencegah peningkatan hak akses tanpa persetujuan. Mapping permission dan default Member pada registrasi dikerjakan pada tahap berikutnya setelah aturan otorisasi ditetapkan.

## 4. Uji dengan Laravel Tinker

```powershell
php artisan tinker
```

Lalu, di dalam Tinker:

```php
\Illuminate\Support\Facades\DB::table('roles')->orderBy('code')->pluck('label', 'code');
\Illuminate\Support\Facades\DB::table('permissions')->count();
\Illuminate\Support\Facades\DB::table('role_permissions')->count();
\Illuminate\Support\Facades\DB::table('user_roles')->count();
exit
```

Harapan: enam role tampil; tiga tabel lainnya berjumlah nol bila belum ada data lain. Nol berarti akses belum diberikan, bukan kesalahan migrasi.

## 5. Pengujian otomatis dan build

```powershell
php artisan test tests/Feature/Modules/Identity/RbacSchemaTest.php
php artisan test
npm run build
```

Target penambahan **5 tests**. Jika baseline tetap 78 tests, total target **83 tests**. Jumlah assertion akan bertambah. Build seharusnya tidak berubah karena tidak ada perubahan frontend.

Pengujian menggunakan `RefreshDatabase`, jadi jalankan dengan konfigurasi testing Laravel. **Jangan gunakan `migrate:fresh`, `db:wipe`, atau `migrate:reset` di database development.**

## 6. Periksa perubahan sebelum Git

```powershell
git status --short
git diff --check
```

Belum perlu commit sampai hasil pengujian dikonfirmasi.

## Rancangan keamanan tahap ini

- `user_roles`: setiap user+role unik; role yang sudah ditugaskan tidak boleh dihapus sembarangan; assignment dapat memiliki `assigned_by`.
- `role_permissions`: setiap pasangan role+permission unik.
- Jika user dihapus melalui fitur akun, keanggotaan role-nya dibersihkan otomatis untuk menghindari orphan records.
- *No implicit privilege*: tidak ada hak SuperAdmin/Editor/Verifier secara otomatis.
- **Belum ada pemeriksaan izin di route aplikasi**. Keamanan dashboard akan diaktifkan pada S2-01C/D melalui policy/gate/middleware dan tes negatif untuk setiap role.

## Berikutnya

S2-01C: Model/contract + matriks izin per role dan mekanisme provisioning akun. S2-01D: middleware dan otorisasi. S2-01E: login/register berdasarkan role yang sudah sah. Jangan mengubah route `/dashboard` untuk membedakan role pada tahap ini.
