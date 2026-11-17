<?php

namespace App\Modules\Scholarship\Domain\Contracts;

interface ScholarshipRepository
{
    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function paginatePublished(
        int $page = 1,
        int $perPage = 12,
        array $filters = []
    ): array;

    /**
     * @return array<string, mixed>|null
     */
    public function findPublishedBySlug(string $slug): ?array;
}