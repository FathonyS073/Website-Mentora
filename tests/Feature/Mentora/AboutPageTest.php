<?php

namespace Tests\Feature\Mentora;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_about_page_is_public_and_renders_mentora_about(): void
    {
        $this->get('/tentang')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/About')
            );
    }

    public function test_about_page_is_available_by_named_route(): void
    {
        $this->assertSame('/tentang', route('about', absolute: false));
    }
}
