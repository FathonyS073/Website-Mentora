<?php

namespace App\Modules\Event\Application\UseCases;

use App\Modules\Event\Domain\Contracts\EventRepository;

final class SearchEvents
{
    public function __construct(
        private readonly EventRepository $repository
    ) {}

    /**
     * Mengambil daftar event publik melalui kontrak repository.
     * Validasi input HTTP akan dilakukan di Presentation Layer.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function execute(array $filters = []): array
    {
        // Halaman harus berupa bilangan bulat positif.
        $page = filter_var(
            $filters['page'] ?? 1,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $page = $page === false ? 1 : $page;

        // Hanya filter yang didukung repository yang diteruskan.
        $normalized = [];

        foreach (['search', 'event_type', 'delivery_mode'] as $key) {
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
