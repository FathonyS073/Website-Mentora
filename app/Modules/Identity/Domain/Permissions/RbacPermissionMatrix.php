<?php

declare(strict_types=1);

namespace App\Modules\Identity\Domain\Permissions;

/**
 * Declarative baseline for the six MENTORA internal roles.
 *
 * Permission grants here are NOT user assignments. Per-resource ownership,
 * guardian consent and editorial workflow constraints must be checked by
 * application policies at the point of use (S2-01D and subsequent modules).
 */
final class RbacPermissionMatrix
{
    /** @return array<string, array{label: string, description: string}> */
    public static function permissions(): array
    {
        return [
            'profile.view' => [
                'label' => 'Lihat profil pribadi',
                'description' => 'Hanya profil milik pengguna yang terautentikasi.',
            ],
            'profile.update' => [
                'label' => 'Ubah profil pribadi',
                'description' => 'Hanya profil milik pengguna yang terautentikasi.',
            ],
            'scholarship.save' => [
                'label' => 'Simpan beasiswa',
                'description' => 'Mengelola daftar beasiswa milik Member sendiri.',
            ],
            'tracker.manage' => [
                'label' => 'Kelola tracker',
                'description' => 'Mengelola proses pendaftaran beasiswa milik Member sendiri.',
            ],
            'matching.view' => [
                'label' => 'Gunakan rekomendasi',
                'description' => 'Mendapatkan eligibility dan rekomendasi dari profil sendiri.',
            ],
            'guardian.links.view' => [
                'label' => 'Lihat hubungan pendampingan',
                'description' => 'Hanya hubungan Guardian–Member yang telah diizinkan.',
            ],
            'guardian.progress.view' => [
                'label' => 'Pantau progres dampingan',
                'description' => 'Hanya data Member yang memberi persetujuan dan masih aktif.',
            ],
            'content.article.draft' => [
                'label' => 'Kelola draft artikel',
                'description' => 'Membuat dan mengedit draft artikel sesuai hak kepemilikan.',
            ],
            'content.event.draft' => [
                'label' => 'Kelola draft kegiatan',
                'description' => 'Membuat dan mengedit draft Event sesuai hak kepemilikan.',
            ],
            'content.scholarship.draft' => [
                'label' => 'Kelola draft beasiswa',
                'description' => 'Membuat dan mengedit draft informasi beasiswa.',
            ],
            'content.review' => [
                'label' => 'Verifikasi konten',
                'description' => 'Menilai sumber dan isi draft, tanpa hak publikasi langsung.',
            ],
            'content.publish' => [
                'label' => 'Publikasikan konten',
                'description' => 'Memublikasikan konten yang sudah memenuhi alur verifikasi.',
            ],
            'users.view' => [
                'label' => 'Lihat daftar pengguna',
                'description' => 'Melihat data pengguna dalam batas operasional yang diizinkan.',
            ],
            'users.manage' => [
                'label' => 'Kelola operasional pengguna',
                'description' => 'Tugas operasional tanpa kewenangan menetapkan role istimewa.',
            ],
            'roles.assign' => [
                'label' => 'Tetapkan role pengguna',
                'description' => 'Hak istimewa untuk mengubah keanggotaan role melalui proses terotorisasi.',
            ],
            'settings.manage' => [
                'label' => 'Kelola konfigurasi sistem',
                'description' => 'Mengatur parameter sistem yang bersifat administratif.',
            ],
            'analytics.view' => [
                'label' => 'Lihat analitik platform',
                'description' => 'Melihat laporan agregat berdasarkan aturan privasi.',
            ],
            'audit.view' => [
                'label' => 'Lihat audit sistem',
                'description' => 'Melihat jejak aktivitas yang diizinkan sesuai kewenangan.',
            ],
        ];
    }

    /** @return array<string, list<string>> */
    public static function grants(): array
    {
        $profile = ['profile.view', 'profile.update'];

        return [
            'member' => [
                ...$profile,
                'scholarship.save',
                'tracker.manage',
                'matching.view',
            ],
            'guardian' => [
                ...$profile,
                'guardian.links.view',
                'guardian.progress.view',
            ],
            'editor' => [
                ...$profile,
                'content.article.draft',
                'content.event.draft',
                'content.scholarship.draft',
            ],
            'verifier' => [
                ...$profile,
                'content.review',
            ],
            'admin' => [
                ...$profile,
                'content.publish',
                'users.view',
                'users.manage',
                'analytics.view',
                'audit.view',
            ],
            'superadmin' => array_keys(self::permissions()),
        ];
    }
}
