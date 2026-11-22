<?php

namespace Tests\Feature\Mentora;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FeaturesPageTest extends TestCase
{
    /**
     * Memastikan pengunjung tanpa login
     * dapat mengakses halaman Fitur MENTORA.
     */
    public function test_features_page_is_public(): void
    {
        $this->get(route('features.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Features/Index')
            );
    }
}