MENTORA — Redirect /dashboard ke /member/dashboard

1. Backup file lama:
   Copy-Item "routes\web.php" "$env:TEMP\MENTORA_web_before_redirect.php"

2. Ekstrak paket ke root D:\Project Website\MENTORA (gunakan -Force).
   Paket menimpa routes/web.php sesuai isi file yang Anda kirim.

3. Jalankan:
   php artisan route:clear
   php artisan route:list --path=dashboard
   php artisan test tests/Feature/Modules/Identity/MemberDashboardTest.php
   php artisan test

4. Login dengan akun Member yang sudah diverifikasi dan buka
   http://127.0.0.1:8000/dashboard
   Seharusnya redirect ke /member/dashboard.

Penjelasan:
- Hanya route /dashboard yang dialihkan bila akun memiliki role member.
- Semua route publik, preview, dan file route eksternal dipertahankan.
- Non-Member tetap melihat Dashboard lama sementara.
- Tidak mengubah database, Service Provider, Login, Register, atau middleware RBAC.
