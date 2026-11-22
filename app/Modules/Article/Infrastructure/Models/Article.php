<?php

namespace App\Modules\Article\Infrastructure\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class Article extends Model
{
    // Menggunakan tabel articles yang dibuat pada S1-04.5A.
    protected $table = 'articles';

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'cover_image_path',
        'source_url',
        'author_id',
        'publication_status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'author_id' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Hanya artikel dengan status published yang tampil secara publik.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('publication_status', 'published');
    }
}
