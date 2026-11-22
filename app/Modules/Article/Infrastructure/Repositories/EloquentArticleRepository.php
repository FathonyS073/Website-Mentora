<?php

namespace App\Modules\Article\Infrastructure\Repositories;

use App\Modules\Article\Domain\Contracts\ArticleRepository;
use App\Modules\Article\Infrastructure\Models\Article;
use Illuminate\Database\Eloquent\Builder;

final class EloquentArticleRepository implements ArticleRepository
{
    /**
     * Daftar artikel publik dengan pencarian, kategori, dan pagination.
     *
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int, current_page: int, last_page: int, per_page: int}
     */
    public function paginatePublished(
        int $page = 1,
        int $perPage = 12,
        array $filters = []
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(60, $perPage));

        // Artikel draft tidak boleh masuk hasil pencarian publik.
        $query = Article::query()->published();

        $search = trim((string) ($filters['search'] ?? ''));

        if ($search !== '') {
            $pattern = '%' . mb_strtolower($search) . '%';

            $query->where(static function (Builder $builder) use ($pattern): void {
                $builder
                    ->whereRaw('LOWER(title) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(excerpt) LIKE ?', [$pattern]);
            });
        }

        $category = trim((string) ($filters['category'] ?? ''));

        if ($category !== '') {
            $query->where('category', $category);
        }

        // NULL published_at ditempatkan setelah artikel bertanggal.
        // CASE kompatibel dengan PostgreSQL maupun SQLite untuk pengujian.
        $paginator = $query
            ->orderByRaw('CASE WHEN published_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'items' => $paginator
                ->getCollection()
                ->map(static fn (Article $article): array => $article->toArray())
                ->values()
                ->all(),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
        ];
    }

    /**
     * Artikel draft maupun slug yang tidak ada menghasilkan null.
     *
     * @return array<string, mixed>|null
     */
    public function findPublishedBySlug(string $slug): ?array
    {
        $article = Article::query()
            ->published()
            ->where('slug', $slug)
            ->first();

        return $article?->toArray();
    }
}
