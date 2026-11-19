<?php

namespace App\Modules\Scholarship\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Scholarship\Application\UseCases\GetPublishedScholarship;
use Inertia\Inertia;
use Inertia\Response;

final class ScholarshipDetailController extends Controller
{
    /**
     * Menampilkan detail beasiswa yang sudah
     * diverifikasi dan dipublikasikan.
     */
    public function __invoke(
        string $slug,
        GetPublishedScholarship $getPublishedScholarship
    ): Response {
        // Application Layer mengambil beasiswa berdasarkan slug.
        $scholarship = $getPublishedScholarship->execute($slug);

        // Jangan tampilkan program yang tidak ditemukan
        // atau belum memenuhi aturan publikasi.
        if ($scholarship === null) {
            abort(404);
        }

        // Mengirim data beasiswa ke halaman Vue melalui Inertia.
        return Inertia::render(
            'Mentora/Scholarships/Show',
            [
                'scholarship' => $scholarship,
            ]
        );
    }
}