<?php

namespace App\Modules\Scholarship\Infrastructure\Repositories;

use App\Modules\Scholarship\Domain\Contracts\ScholarshipRepository;
use App\Modules\Scholarship\Infrastructure\Models\Scholarship;
use Illuminate\Database\Eloquent\Builder;

class EloquentScholarshipRepository implements ScholarshipRepository
{
    public function paginatePublished(
        int $page = 1,
        int $perPage = 12,
        array $filters = []
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));

        $query = Scholarship::query()
            ->publiclyVisible();

        // Search by scholarship title or provider.
        $search = $filters['search'] ?? null;

        if (is_string($search) && trim($search) !== '') {
            $keyword = '%' . mb_strtolower(trim($search)) . '%';

            $query->where(
                function (Builder $builder) use ($keyword): void {
                    $builder
                        ->whereRaw(
                            'LOWER(title) LIKE ?',
                            [$keyword]
                        )
                        ->orWhereRaw(
                            'LOWER(provider_name) LIKE ?',
                            [$keyword]
                        );
                }
            );
        }

        // Filter by education level.
        $level = $filters['education_level'] ?? null;

        if (is_string($level) && trim($level) !== '') {
            $query->whereJsonContains(
                'education_levels',
                trim($level)
            );
        }

        // Filter by location and funding.
        foreach (['location_scope', 'funding_type'] as $field) {
            $value = $filters[$field] ?? null;

            if (is_string($value) && trim($value) !== '') {
                $query->where($field, trim($value));
            }
        }

        // Filter by field of study.
        $fieldOfStudy = $filters['field_of_study'] ?? null;

        if (
            is_string($fieldOfStudy) &&
            trim($fieldOfStudy) !== ''
        ) {
            $query->whereJsonContains(
                'fields_of_study',
                trim($fieldOfStudy)
            );
        }

        $result = $query
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