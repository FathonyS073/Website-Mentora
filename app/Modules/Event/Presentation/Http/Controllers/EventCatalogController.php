<?php

namespace App\Modules\Event\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Event\Application\UseCases\SearchEvents;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class EventCatalogController extends Controller
{
    /**
     * Katalog publik. Query dibaca dan divalidasi di Presentation Layer.
     */
    public function __invoke(Request $request, SearchEvents $searchEvents): Response
    {
        $validated = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:150'],
            'event_type' => ['sometimes', 'nullable', 'string', 'max:30'],
            'delivery_mode' => ['sometimes', 'nullable', 'in:online,offline,hybrid'],
            'page' => ['sometimes', 'integer', 'min:1', 'max:100000'],
        ]);

        $filters = [
            'search' => (string) ($validated['search'] ?? ''),
            'event_type' => (string) ($validated['event_type'] ?? ''),
            'delivery_mode' => (string) ($validated['delivery_mode'] ?? ''),
        ];

        if (isset($validated['page'])) {
            $filters['page'] = (int) $validated['page'];
        }

        return Inertia::render('Mentora/Events/Index', [
            'events' => $searchEvents->execute($filters),
            'filters' => [
                'search' => $filters['search'],
                'event_type' => $filters['event_type'],
                'delivery_mode' => $filters['delivery_mode'],
            ],
        ]);
    }
}
