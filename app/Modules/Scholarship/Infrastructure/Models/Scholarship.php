<?php

namespace App\Modules\Scholarship\Infrastructure\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    // Menghubungkan model dengan tabel scholarships.
    protected $table = 'scholarships';

    // Menentukan kolom yang dapat diisi melalui Eloquent.
    protected $fillable = [
        'title',
        'slug',
        'provider_name',
        'summary',
        'description',
        'education_levels',
        'fields_of_study',
        'location_scope',
        'funding_type',
        'opens_at',
        'closes_at',
        'source_url',
        'application_url',
        'verification_status',
        'publication_status',
        'published_at',

        // Kolom tambahan untuk halaman Detail Beasiswa.
        'requirements',
        'benefits',
        'required_documents',
    ];

    // Menentukan konversi tipe data dari database ke PHP.
    protected function casts(): array
    {
        return [
            'education_levels' => 'array',
            'fields_of_study' => 'array',
            'opens_at' => 'date',
            'closes_at' => 'date',
            'published_at' => 'datetime',

            // Konversi tiga kolom JSON menjadi array PHP.
            'requirements' => 'array',
            'benefits' => 'array',
            'required_documents' => 'array',
        ];
    }

    /**
     * Membatasi akses publik hanya untuk beasiswa
     * yang sudah diterbitkan dan diverifikasi.
     */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('publication_status', 'published')
            ->where('verification_status', 'verified');
    }
}