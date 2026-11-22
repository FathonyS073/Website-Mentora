<?php

namespace App\Modules\Article\Domain\Contracts;

interface ArticleRepository
{
    /**
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int, current_page: int, last_page: int, per_page: int}
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
