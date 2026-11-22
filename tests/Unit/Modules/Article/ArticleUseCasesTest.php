<?php

namespace Tests\Unit\Modules\Article;

use App\Modules\Article\Application\UseCases\GetPublishedArticle;
use App\Modules\Article\Application\UseCases\SearchArticles;
use App\Modules\Article\Domain\Contracts\ArticleRepository;
use PHPUnit\Framework\TestCase;

final class ArticleUseCasesTest extends TestCase
{
    public function test_search_uses_default_page_size_and_no_filters(): void
    {
        $repository = new FakeArticleRepository();
        $result = (new SearchArticles($repository))->execute();

        self::assertSame(1, $repository->receivedPage);
        self::assertSame(12, $repository->receivedPerPage);
        self::assertSame([], $repository->receivedFilters);
        self::assertSame(0, $result['total']);
    }

    public function test_search_normalizes_supported_filters(): void
    {
        $repository = new FakeArticleRepository();

        (new SearchArticles($repository))->execute([
            'page' => '3',
            'search' => '  beasiswa luar negeri  ',
            'category' => '  panduan  ',
            'publication_status' => 'draft',
            'ignored' => 'value',
        ]);

        self::assertSame(3, $repository->receivedPage);
        self::assertSame([
            'search' => 'beasiswa luar negeri',
            'category' => 'panduan',
        ], $repository->receivedFilters);
    }

    public function test_search_rejects_invalid_page_and_non_string_filters(): void
    {
        $repository = new FakeArticleRepository();

        (new SearchArticles($repository))->execute([
            'page' => ['not-a-page'],
            'search' => ['not-a-search'],
            'category' => ' ',
        ]);

        self::assertSame(1, $repository->receivedPage);
        self::assertSame([], $repository->receivedFilters);
    }

    public function test_detail_trims_slug_before_repository_lookup(): void
    {
        $repository = new FakeArticleRepository();
        $repository->articleToReturn = ['slug' => 'tips-beasiswa'];

        $result = (new GetPublishedArticle($repository))->execute('  tips-beasiswa  ');

        self::assertSame('tips-beasiswa', $repository->receivedSlug);
        self::assertSame(['slug' => 'tips-beasiswa'], $result);
    }

    public function test_empty_slug_returns_null_without_query(): void
    {
        $repository = new FakeArticleRepository();

        $result = (new GetPublishedArticle($repository))->execute('   ');

        self::assertNull($result);
        self::assertNull($repository->receivedSlug);
    }
}

/**
 * Repository palsu untuk menguji use case tanpa database.
 */
final class FakeArticleRepository implements ArticleRepository
{
    public ?int $receivedPage = null;
    public ?int $receivedPerPage = null;

    /** @var array<string, mixed>|null */
    public ?array $receivedFilters = null;

    public ?string $receivedSlug = null;

    /** @var array<string, mixed>|null */
    public ?array $articleToReturn = null;

    public function paginatePublished(
        int $page = 1,
        int $perPage = 12,
        array $filters = []
    ): array {
        $this->receivedPage = $page;
        $this->receivedPerPage = $perPage;
        $this->receivedFilters = $filters;

        return [
            'items' => [],
            'total' => 0,
            'current_page' => $page,
            'last_page' => 1,
            'per_page' => $perPage,
        ];
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        $this->receivedSlug = $slug;

        return $this->articleToReturn;
    }
}
