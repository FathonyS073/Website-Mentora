<?php

namespace App\Modules\Event\Domain\Contracts;

interface EventRepository
{
    /**
     * Daftar event yang dipublikasikan, dengan pencarian dan pagination.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function paginatePublished(
        int $page = 1,
        int $perPage = 12,
        array $filters = []
    ): array;

    /**
     * Detail satu event publik berdasarkan slug.
     *
     * @return array<string, mixed>|null
     */
    public function findPublishedBySlug(string $slug): ?array;
}
