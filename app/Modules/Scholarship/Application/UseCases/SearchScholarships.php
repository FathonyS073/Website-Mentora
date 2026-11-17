<?php

namespace App\Modules\Scholarship\Application\UseCases;

use App\Modules\Scholarship\Domain\Contracts\ScholarshipRepository;

final class SearchScholarships
{
    public function __construct(
        private readonly ScholarshipRepository $repository
    ) {}

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function execute(array $filters = []): array
    {
        $page = filter_var(
            $filters['page'] ?? 1,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($page === false) {
            $page = 1;
        }

        return $this->repository->paginatePublished(
            page: $page,
            perPage: 12,
            filters: $filters
        );
    }
}