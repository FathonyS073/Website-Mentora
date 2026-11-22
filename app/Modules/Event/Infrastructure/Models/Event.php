<?php

namespace App\Modules\Event\Infrastructure\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class Event extends Model
{
    // Tabel utama untuk informasi kegiatan MENTORA.
    protected $table = 'events';

    // Kolom yang dapat diisi melalui Eloquent.
    protected $fillable = [
        'title',
        'slug',
        'organizer_name',
        'summary',
        'description',
        'event_type',
        'delivery_mode',
        'location_name',
        'timezone',
        'starts_at',
        'ends_at',
        'registration_closes_at',
        'source_url',
        'registration_url',
        'cover_image_path',
        'publication_status',
        'published_at',
    ];

    // Mengubah kolom tanggal menjadi objek Carbon.
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'registration_closes_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    // Event yang belum diterbitkan tidak boleh muncul pada halaman publik.
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('publication_status', 'published');
    }
}
