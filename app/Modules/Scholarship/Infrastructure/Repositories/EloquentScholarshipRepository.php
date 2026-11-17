<?php

namespace App\Modules\Scholarship\Infrastructure\Repositories;

use App\Modules\Scholarship\Domain\Contracts\ScholarshipRepository;
use App\Modules\Scholarship\Infrastructure\Models\Scholarship;


class EloquentScholarshipRepository implements ScholarshipRepository
{
    public function paginatePublished(
        int $page = 1,
        int $perPage = 12
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));

        $result = Scholarship::query()
            ->publiclyVisible()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'items' => $result->getCollection()
                ->map(
                    fn (Scholarship $scholarship): array =>
                        $scholarship->toArray()
                )
                ->values()
                ->all(),

            'total' => $result->total(),
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
            'per_page' => $result->perPage(),
        ];
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        $scholarship = Scholarship::query()
            ->publiclyVisible()
            ->where('slug', $slug)
            ->first();

        return $scholarship?->toArray();
    }
}