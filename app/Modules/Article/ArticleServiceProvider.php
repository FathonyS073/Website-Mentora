<?php

namespace App\Modules\Article;

use App\Modules\Article\Domain\Contracts\ArticleRepository;
use App\Modules\Article\Infrastructure\Repositories\EloquentArticleRepository;
use Illuminate\Support\ServiceProvider;

final class ArticleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Dependency injection: kontrak Domain -> implementasi Infrastructure.
        $this->app->bind(
            ArticleRepository::class,
            EloquentArticleRepository::class
        );
    }
}
