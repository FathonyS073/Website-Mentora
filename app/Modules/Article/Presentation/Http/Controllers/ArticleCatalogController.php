<?php

namespace App\Modules\Article\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Article\Application\UseCases\SearchArticles;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ArticleCatalogController extends Controller
{
    /**
     * Katalog artikel publik. Validasi query dilakukan pada Presentation Layer.
     */
    public function __invoke(Request $request, SearchArticles $searchArticles): Response
    {
        $validated = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:150'],
            'category' => ['sometimes', 'nullable', 'string', 'max:60'],
            'page' => ['sometimes', 'integer', 'min:1', 'max:100000'],
        ]);

        $filters = [
            'search' => trim((string) ($validated['search'] ?? '')),
            'category' => trim((string) ($validated['category'] ?? '')),
        ];

        if (isset($validated['page'])) {
            $filters['page'] = (int) $validated['page'];
        }

        return Inertia::render('Mentora/Articles/Index', [
            'articles' => $searchArticles->execute($filters),
            'filters' => [
                'search' => $filters['search'],
                'category' => $filters['category'],
            ],
        ]);
    }
}
