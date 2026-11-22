MENTORA — S1-04.4C Event Application Layer

Paket ini berisi 2 file baru:
- app/Modules/Event/Application/UseCases/SearchEvents.php
- app/Modules/Event/Application/UseCases/GetPublishedEvent.php

Cara pemasangan (PowerShell, dari root MENTORA):
  Expand-Archive -Path "$env:USERPROFILE\Downloads\MENTORA_Event_Application_S1-04.4C.zip" -DestinationPath "D:\Project Website\MENTORA" -Force

Tidak perlu membuat migration, route, controller, ataupun mengubah bootstrap/providers.php.
EventServiceProvider dari S1-04.4B sudah mengikat EventRepository.

Verifikasi (php artisan tinker):
  $search = app(\App\Modules\Event\Application\UseCases\SearchEvents::class);
  $search->execute();
  $detail = app(\App\Modules\Event\Application\UseCases\GetPublishedEvent::class);
  $detail->execute('event-yang-belum-ada');
  exit

Tanpa event published, hasil daftar adalah items=[] dan total=0; slug yang tidak ada menghasilkan null.
Kemudian jalankan php artisan test.
