<?php

namespace App\Modules\Event\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Event\Application\UseCases\GetPublishedEvent;
use Inertia\Inertia;
use Inertia\Response;

final class EventDetailController extends Controller
{
    /**
     * Hanya event published yang boleh diakses melalui URL publik.
     */
    public function __invoke(string $slug, GetPublishedEvent $getPublishedEvent): Response
    {
        $event = $getPublishedEvent->execute($slug);

        if ($event === null) {
            abort(404);
        }

        return Inertia::render('Mentora/Events/Show', [
            'event' => $event,
        ]);
    }
}
