<?php

namespace Tests\Feature\Modules\Event;

use App\Modules\Event\Infrastructure\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EventPublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function createEvent(string $slug, array $overrides = []): Event
    {
        return Event::query()->create(array_merge([
            'title' => 'Webinar Contoh',
            'slug' => $slug,
            'organizer_name' => 'Penyelenggara Pengujian',
            'summary' => 'Ringkasan pengujian.',
            'event_type' => 'webinar',
            'delivery_mode' => 'online',
            'timezone' => 'Asia/Jakarta',
            'starts_at' => now()->addDays(2),
            'source_url' => 'https://example.org/event',
            'publication_status' => 'published',
            'published_at' => now(),
        ], $overrides));
    }

    public function test_guest_can_view_empty_event_catalog(): void
    {
        $this->get('/event')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Events/Index')
                ->where('events.total', 0)
                ->etc());
    }

    public function test_catalog_filters_published_events_only(): void
    {
        $this->createEvent('webinar-publik');
        $this->createEvent('webinar-draft', ['publication_status' => 'draft']);
        $this->createEvent('workshop-publik', [
            'event_type' => 'workshop',
            'delivery_mode' => 'offline',
        ]);

        $this->get('/event?event_type=webinar&delivery_mode=online')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Events/Index')
                ->where('events.total', 1)
                ->has('events.items', 1)
                ->where('events.items.0.slug', 'webinar-publik')
                ->etc());
    }

    public function test_guest_can_view_published_event_detail(): void
    {
        $this->createEvent('webinar-publik');

        $this->get('/event/webinar-publik')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Events/Show')
                ->where('event.slug', 'webinar-publik')
                ->etc());
    }

    public function test_draft_event_detail_returns_404(): void
    {
        $this->createEvent('webinar-draft', ['publication_status' => 'draft']);
        $this->get('/event/webinar-draft')->assertNotFound();
    }

    public function test_missing_event_detail_returns_404(): void
    {
        $this->get('/event/tidak-ada')->assertNotFound();
    }
}
