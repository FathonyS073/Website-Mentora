<?php

namespace App\Modules\Scholarship\Application\UseCases;

use App\Modules\Scholarship\Domain\Contracts\ScholarshipRepository;

final class GetPublishedScholarship
{
    public function __construct(
        private readonly ScholarshipRepository $repository
    ) {}

    /**
     * Retrieve a verified and published scholarship by slug.
     *
     * @return array<string, mixed>|null
     */
    public function execute(string $slug): ?array
    {
        return $this->repository->findPublishedBySlug($slug);
    }
}