<?php

namespace Tests\Feature\Mentora;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_homepage_is_public_and_renders_mentora_home(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Home')
            );
    }
}
