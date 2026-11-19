<?php

namespace Tests\Feature\Modules\Scholarship;

use App\Modules\Scholarship\Infrastructure\Models\Scholarship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ScholarshipDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat data beasiswa khusus pengujian.
     *
     * Data ini menggunakan database testing dan
     * tidak dimasukkan ke database development MENTORA.
     */
    private function createScholarship(
        string $slug,
        array $overrides = []
    ): Scholarship {
        return Scholarship::query()->create(array_merge([
            'title' => 'Beasiswa Pengujian MENTORA',
            'slug' => $slug,
            'provider_name' => 'Penyelenggara Pengujian',
            'summary' => 'Ringkasan beasiswa pengujian.',
            'description' => 'Deskripsi lengkap program pengujian.',
            'education_levels' => ['S1', 'S2'],
            'fields_of_study' => ['sains-teknologi'],
            'location_scope' => 'indonesia',
            'funding_type' => 'fully_funded',
            'source_url' => 'https://example.org/scholarship',
            'publication_status' => 'published',
            'verification_status' => 'verified',
            'published_at' => now(),
        ], $overrides));
    }

    /**
     * Beasiswa yang verified dan published
     * boleh dilihat oleh pengunjung tanpa login.
     */
    public function test_guest_can_view_published_scholarship(): void
    {
        $this->createScholarship('program-terverifikasi');

        $this->get('/beasiswa/program-terverifikasi')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Scholarships/Show')
                ->where(
                    'scholarship.slug',
                    'program-terverifikasi'
                )
                ->where(
                    'scholarship.title',
                    'Beasiswa Pengujian MENTORA'
                )
                ->etc()
            );
    }

    /**
     * Slug yang tidak tersedia harus
     * menghasilkan HTTP 404.
     */
    public function test_nonexistent_scholarship_returns_404(): void
    {
        $this->get('/beasiswa/program-tidak-ada')
            ->assertNotFound();
    }

    /**
     * Beasiswa draft tidak boleh dibuka publik,
     * meskipun pengguna mengetahui URL-nya.
     */
    public function test_draft_scholarship_returns_404(): void
    {
        $this->createScholarship('program-draft', [
            'publication_status' => 'draft',
        ]);

        $this->get('/beasiswa/program-draft')
            ->assertNotFound();
    }

    /**
     * Beasiswa yang belum diverifikasi
     * juga tidak boleh ditampilkan ke publik.
     */
    public function test_unverified_scholarship_returns_404(): void
    {
        $this->createScholarship('program-pending', [
            'verification_status' => 'pending',
        ]);

        $this->get('/beasiswa/program-pending')
            ->assertNotFound();
    }
}