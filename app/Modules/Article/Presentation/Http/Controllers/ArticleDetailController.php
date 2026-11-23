<?php

namespace App\Modules\Article\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Article\Application\UseCases\GetPublishedArticle;
use Inertia\Inertia;
use Inertia\Response;

final class ArticleDetailController extends Controller
{
    /** Artikel draft atau slug yang tidak ditemukan tidak bisa dibuka publik. */
    public function __invoke(string $slug, GetPublishedArticle $getPublishedArticle): Response
    {
        $article = $getPublishedArticle->execute($slug);

        abort_if($article === null, 404);

        return Inertia::render('Mentora/Articles/Show', [
            'article' => $article,
        ]);
    }
}
