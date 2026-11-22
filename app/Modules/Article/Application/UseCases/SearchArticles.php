<?php

namespace App\Modules\Article\Application\UseCases;

use App\Modules\Article\Domain\Contracts\ArticleRepository;

final class SearchArticles
{
    public function __construct(
        private readonly ArticleRepository $repository
    ) {}

    /**
     * Mengambil daftar artikel publik. Validasi input HTTP berada di controller.
     * Hanya filter yang didukung repository diteruskan.
     *
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int, current_page: int, last_page: int, per_page: int}
     */
    public function execute(array $filters = []): array
    {
        $rawPage = $filters['page'] ?? 1;

        $page = (is_int($rawPage) || is_string($rawPage))
            ? filter_var(
                $rawPage,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 100000]]
            )
            : false;

        $page = $page === false ? 1 : $page;

        $normalized = [];

        foreach (['search', 'category'] as $key) {
            $value = $filters[$key] ?? null;

            if (! is_string($value)) {
                continue;
            }

            $value = trim($value);

            if ($value !== '') {
                $normalized[$key] = $value;
            }
        }

        return $this->repository->paginatePublished(
            page: $page,
            perPage: 12,
            filters: $normalized
        );
    }
}
