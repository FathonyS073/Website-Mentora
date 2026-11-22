<?php

namespace App\Modules\Event\Application\UseCases;

use App\Modules\Event\Domain\Contracts\EventRepository;

final class GetPublishedEvent
{
    public function __construct(
        private readonly EventRepository $repository
    ) {}

    /**
     * Mengambil detail event published berdasarkan slug.
     * Mengembalikan null jika slug kosong atau event tidak publik.
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
