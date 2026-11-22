# MENTORA — S1-04.4F Detail Event + Preview Lokal

Paket ini hanya memperbarui dua file Vue:

- `resources/js/pages/Mentora/Events/Show.vue`: halaman detail Event responsif, informasi jadwal sesuai zona waktu, lokasi, dan tautan resmi aman.
- `resources/js/components/mentora/EventCard.vue`: kartu Event asli menuju `/event/{slug}`; kartu preview menuju `/ui-preview/event-detail/{sample}`.

Tidak mengubah `Index.vue`, `CardPreview.vue`, Homepage, Beasiswa, controller, repository, atau database.

## 1. Cadangan dan ekstraksi

Buka terminal PowerShell dari `D:\Project Website\MENTORA`:

```powershell
Set-Location "D:\Project Website\MENTORA"

Copy-Item "resources\js\pages\Mentora\Events\Show.vue" "$env:TEMP\MENTORA_Event_Show_before_S1-04.4F.vue"
Copy-Item "resources\js\components\mentora\EventCard.vue" "$env:TEMP\MENTORA_EventCard_before_S1-04.4F.vue"

Expand-Archive `
    -Path "$env:USERPROFILE\Downloads\MENTORA_Event_Detail_S1-04.4F.zip" `
    -DestinationPath "D:\Project Website\MENTORA" `
    -Force
```

## 2. Tambahkan route preview pada `routes/web.php`

Cari blok `if (app()->environment('local')) { ... }` yang SUDAH ADA.
Tambahkan route ini di dalam blok tersebut. Jangan mengganti keseluruhan file dan jangan menambah blok `if` baru. Pertahankan route preview lain serta `/event` dan `/event/{slug}` di luar blok `local`.

```php
// Khusus local: contoh detail Event, tanpa menulis ke database.
Route::get('/ui-preview/event-detail/{sample}', function (string $sample) {
    $samples = [
        'preview-event-webinar' => [
            'id' => -1,
            'title' => '[CONTOH] Webinar Strategi Mencari Beasiswa',
            'slug' => 'preview-event-webinar',
            'organizer_name' => 'Penyelenggara Contoh A',
            'summary' => 'Webinar demonstrasi untuk memeriksa tampilan informasi dan jadwal.',
            'description' => "Ini adalah uraian contoh halaman detail webinar MENTORA.\nInformasi ini bukan kegiatan nyata atau undangan pendaftaran.",
            'event_type' => 'webinar',
            'delivery_mode' => 'online',
            'location_name' => null,
            'timezone' => 'Asia/Jakarta',
            'starts_at' => '2027-01-15T02:00:00.000Z',
            'ends_at' => '2027-01-15T04:00:00.000Z',
            'registration_closes_at' => '2027-01-14T16:59:00.000Z',
            'source_url' => null,
            'registration_url' => null,
            'cover_image_path' => null,
        ],
        'preview-event-workshop' => [
            'id' => -2,
            'title' => '[CONTOH] Workshop Persiapan Dokumen Beasiswa',
            'slug' => 'preview-event-workshop',
            'organizer_name' => 'Penyelenggara Contoh B',
            'summary' => 'Workshop contoh untuk menguji tampilan kegiatan tatap muka.',
            'description' => 'Konten demonstrasi untuk memeriksa lokasi, metode, dan informasi pendaftaran workshop.',
            'event_type' => 'workshop',
            'delivery_mode' => 'offline',
            'location_name' => 'Lokasi contoh, Indonesia',
            'timezone' => 'Asia/Jakarta',
            'starts_at' => '2027-01-22T06:00:00.000Z',
            'ends_at' => '2027-01-22T09:00:00.000Z',
            'registration_closes_at' => '2027-01-21T16:59:00.000Z',
            'source_url' => null,
            'registration_url' => null,
            'cover_image_path' => null,
        ],
        'preview-event-seminar' => [
            'id' => -3,
            'title' => '[CONTOH] Seminar Pendidikan dan Peluang Studi',
            'slug' => 'preview-event-seminar',
            'organizer_name' => 'Penyelenggara Contoh C',
            'summary' => 'Seminar contoh untuk memeriksa informasi kegiatan hybrid.',
            'description' => 'Informasi demonstrasi untuk pengujian halaman detail dan konsistensi desain MENTORA.',
            'event_type' => 'seminar',
            'delivery_mode' => 'hybrid',
            'location_name' => 'Lokasi demonstrasi',
            'timezone' => 'Asia/Jakarta',
            'starts_at' => '2027-02-05T01:30:00.000Z',
            'ends_at' => '2027-02-05T04:00:00.000Z',
            'registration_closes_at' => '2027-02-04T16:59:00.000Z',
            'source_url' => null,
            'registration_url' => null,
            'cover_image_path' => null,
        ],
    ];

    abort_unless(array_key_exists($sample, $samples), 404);

    return \Inertia\Inertia::render('Mentora/Events/Show', [
        'event' => $samples[$sample],
        'preview' => true,
    ]);
})->name('ui-preview.event-detail');
```

## 3. AppLayout

`resources/js/app.ts` seharusnya sudah mengecualikan `Mentora/Events/Show` dari `AppLayout` sejak S1-04.4D. Tidak perlu edit lagi jika benar.

## 4. Verifikasi

```powershell
php artisan route:clear
php artisan route:list --path=ui-preview
php artisan test
```

- Buka `http://127.0.0.1:8000/ui-preview/event-cards`.
- Klik `Detail Contoh` pada webinar, workshop, dan seminar satu per satu.
- Setiap kartu harus membuka URL `/ui-preview/event-detail/preview-event-...` yang berbeda.
- Pastikan banner kuning `PREVIEW LOKAL` dan badge `Data Contoh` terlihat.
- Kembali ke `/event` melalui tombol `Kembali ke Katalog Event`.
- Uji juga dengan lebar layar mobile (Chrome DevTools).

`npm run dev` digunakan selama pengembangan. `npm run build` bisa dijalankan pada checkpoint S1-04.4G.

**Jangan memakai route demo untuk event riil.** Event nyata hanya diakses lewat `/event/{slug}` yang memerlukan status `published`. Tautan resmi pada data contoh dinonaktifkan.
