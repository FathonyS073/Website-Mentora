<?php

namespace App\Modules\Article\Application\UseCases;

use App\Modules\Article\Domain\Contracts\ArticleRepository;

final class GetPublishedArticle
{
    public function __construct(
        private readonly ArticleRepository $repository
    ) {}

    /**
     * Mengambil artikel terpublikasi berdasarkan slug.
     * Repository mengembalikan null untuk draft atau slug tak ditemukan.
     *
     * @return array<string, mixed>|null
     */
    public function execute(string $slug): ?array
    {
        $slug = trim($slug);

        if ($slug === '') {
            return null;
        }

        return $this->repository->findPublishedBySlug($slug);
    }
}
