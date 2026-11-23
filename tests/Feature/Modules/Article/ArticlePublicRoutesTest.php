<?php

namespace Tests\Feature\Modules\Article;

use App\Modules\Article\Infrastructure\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ArticlePublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function createArticle(string $slug, array $overrides = []): Article
    {
        return Article::query()->create(array_merge([
            'title' => 'Panduan Beasiswa Contoh',
            'slug' => $slug,
            'category' => 'Panduan',
            'excerpt' => 'Ringkasan informasi contoh.',
            'content' => 'Isi artikel untuk pengujian otomatis.',
            'publication_status' => 'published',
            'published_at' => now(),
        ], $overrides));
    }

    public function test_guest_can_view_empty_article_catalog(): void
    {
        $this->get('/artikel')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Articles/Index')
                ->where('articles.total', 0)
                ->etc());
    }

    public function test_catalog_only_shows_published_articles(): void
    {
        $this->createArticle('panduan-publik');
        $this->createArticle('artikel-draft', ['publication_status' => 'draft']);

        $this->get('/artikel')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Articles/Index')
                ->where('articles.total', 1)
                ->has('articles.items', 1)
                ->where('articles.items.0.slug', 'panduan-publik')
                ->etc());
    }

    public function test_catalog_search_matches_title_or_excerpt(): void
    {
        $this->createArticle('judul-matching', ['title' => 'Tips Menulis Esai']);
        $this->createArticle('ringkasan-matching', [
            'title' => 'Persiapan Dokumen',
            'excerpt' => 'Bagaimana menulis esai beasiswa yang baik.',
        ]);
        $this->createArticle('tidak-cocok', [
            'title' => 'Informasi Pendaftaran', 'excerpt' => 'Dokumen dan jadwal.',
        ]);

        $this->get('/artikel?search=EsAi')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Articles/Index')
                ->where('articles.total', 2)
                ->has('articles.items', 2)
                ->etc());
    }

    public function test_catalog_filters_category(): void
    {
        $this->createArticle('panduan', ['category' => 'Panduan']);
        $this->createArticle('berita', ['category' => 'Berita']);

        $this->get('/artikel?category=Berita')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Articles/Index')
                ->where('articles.total', 1)
                ->where('articles.items.0.slug', 'berita')
                ->etc());
    }

    public function test_catalog_combines_search_and_category(): void
    {
        $this->createArticle('tepat', [
            'title' => 'Panduan Wawancara', 'category' => 'Panduan',
        ]);
        $this->createArticle('kategori-berbeda', [
            'title' => 'Wawancara Alumni', 'category' => 'Berita',
        ]);
        $this->createArticle('judul-berbeda', [
            'title' => 'Persiapan Administrasi', 'category' => 'Panduan',
        ]);

        $this->get('/artikel?search=Wawancara&category=Panduan')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Articles/Index')
                ->where('articles.total', 1)
                ->where('articles.items.0.slug', 'tepat')
                ->etc());
    }

    public function test_catalog_supports_pagination(): void
    {
        foreach (range(1, 13) as $number) {
            $this->createArticle('artikel-' . $number);
        }

        $this->get('/artikel?page=2')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Articles/Index')
                ->where('articles.total', 13)
                ->where('articles.current_page', 2)
                ->has('articles.items', 1)
                ->etc());
    }

    public function test_guest_can_view_published_article_detail(): void
    {
        $this->createArticle('panduan-publik');

        $this->get('/artikel/panduan-publik')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mentora/Articles/Show')
                ->where('article.slug', 'panduan-publik')
                ->etc());
    }

    public function test_draft_article_detail_returns_404(): void
    {
        $this->createArticle('artikel-draft', ['publication_status' => 'draft']);
        $this->get('/artikel/artikel-draft')->assertNotFound();
    }

    public function test_missing_article_detail_returns_404(): void
    {
        $this->get('/artikel/tidak-ada')->assertNotFound();
    }
}
