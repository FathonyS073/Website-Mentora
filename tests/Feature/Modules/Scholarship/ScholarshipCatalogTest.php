<?php

namespace Tests\Feature\Modules\Scholarship;

use App\Modules\Scholarship\Infrastructure\Models\Scholarship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ScholarshipCatalogTest extends TestCase
{
    use RefreshDatabase;
    private function createScholarship(
        string $slug,
        array $overrides = []
    ): Scholarship {
        return Scholarship::query()->create(array_merge([
            'title' => 'Beasiswa Contoh',
            'slug' => $slug,
            'provider_name' => 'Penyelenggara Uji',
            'summary' => 'Program untuk pengujian katalog.',
            'education_levels' => ['S1', 'S2'],
            'fields_of_study' => ['Teknik'],
            'location_scope' => 'indonesia',
            'funding_type' => 'fully_funded',
            'source_url' => 'https://example.org/beasiswa',
            'publication_status' => 'published',
            'verification_status' => 'verified',
            'published_at' => now(),
        ], $overrides));
    }

    public function test_guest_can_view_catalog(): void
    {
        $this->get('/beasiswa')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Scholarships/Index')
                ->where('scholarships.total', 0)
                ->etc()
            );
    }

    public function test_catalog_only_shows_verified_and_published(): void
    {
        $this->createScholarship('visible');

        $this->createScholarship('draft', [
            'publication_status' => 'draft',
        ]);

        $this->createScholarship('pending', [
            'verification_status' => 'pending',
        ]);

        $this->get('/beasiswa')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Scholarships/Index')
                ->where('scholarships.total', 1)
                ->has('scholarships.items', 1)
                ->where('scholarships.items.0.slug', 'visible')
                ->etc()
            );
    }

    public function test_search_and_education_filter_work(): void
    {
        $this->createScholarship('lpdp-test', [
            'title' => 'Beasiswa LPDP',
            'education_levels' => ['S2', 'S3'],
        ]);

        $this->createScholarship('sarjana-test', [
            'title' => 'Beasiswa Sarjana',
            'education_levels' => ['S1'],
        ]);

        $this->get('/beasiswa?search=LPDP&education_level=S2')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Scholarships/Index')
                ->where('scholarships.total', 1)
                ->where('scholarships.items.0.slug', 'lpdp-test')
                ->etc()
            );

        $this->get('/beasiswa?search=LPDP&education_level=S1')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Scholarships/Index')
                ->where('scholarships.total', 0)
                ->etc()
            );
    }

    public function test_catalog_pagination_works(): void
    {
        for ($i = 1; $i <= 13; $i++) {
            $this->createScholarship("scholarship-{$i}");
        }

        $this->get('/beasiswa?page=2')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Scholarships/Index')
                ->where('scholarships.total', 13)
                ->where('scholarships.current_page', 2)
                ->where('scholarships.last_page', 2)
                ->has('scholarships.items', 1)
                ->etc()
            );
    }

    public function test_combined_filters_only_return_matching_scholarships(): void
    {
        $this->createScholarship('matching-program', [
            'title' => 'Program Teknik S2',
            'education_levels' => ['S2'],
            'location_scope' => 'indonesia',
            'funding_type' => 'fully_funded',
            'fields_of_study' => ['sains-teknologi'],
        ]);

        $this->createScholarship('different-location', [
            'education_levels' => ['S2'],
            'location_scope' => 'international',
            'funding_type' => 'fully_funded',
            'fields_of_study' => ['sains-teknologi'],
        ]);

        $this->createScholarship('different-funding', [
            'education_levels' => ['S2'],
            'location_scope' => 'indonesia',
            'funding_type' => 'partially_funded',
            'fields_of_study' => ['sains-teknologi'],
        ]);

        $this->createScholarship('different-field', [
            'education_levels' => ['S2'],
            'location_scope' => 'indonesia',
            'funding_type' => 'fully_funded',
            'fields_of_study' => ['kesehatan'],
        ]);

        $this->get('/beasiswa?' . http_build_query([
            'education_level' => 'S2',
            'location_scope' => 'indonesia',
            'funding_type' => 'fully_funded',
            'field_of_study' => 'sains-teknologi',
        ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Scholarships/Index')
                ->where('scholarships.total', 1)
                ->has('scholarships.items', 1)
                ->where(
                    'scholarships.items.0.slug',
                    'matching-program'
                )
                ->etc()
            );
    }

    public function test_combined_filters_support_pagination(): void
    {
        for ($i = 1; $i <= 13; $i++) {
            $this->createScholarship("matching-{$i}", [
                'education_levels' => ['S2'],
                'location_scope' => 'indonesia',
                'funding_type' => 'fully_funded',
                'fields_of_study' => ['sains-teknologi'],
            ]);
        }

        $this->createScholarship('excluded-program', [
            'education_levels' => ['S2'],
            'location_scope' => 'international',
            'funding_type' => 'fully_funded',
            'fields_of_study' => ['sains-teknologi'],
        ]);

        $this->get('/beasiswa?' . http_build_query([
            'education_level' => 'S2',
            'location_scope' => 'indonesia',
            'funding_type' => 'fully_funded',
            'field_of_study' => 'sains-teknologi',
            'page' => 2,
        ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Scholarships/Index')
                ->where('scholarships.total', 13)
                ->where('scholarships.current_page', 2)
                ->where('scholarships.last_page', 2)
                ->has('scholarships.items', 1)
                ->where('filters.location_scope', 'indonesia')
                ->where('filters.funding_type', 'fully_funded')
                ->where('filters.field_of_study', 'sains-teknologi')
                ->etc()
            );
    }
}