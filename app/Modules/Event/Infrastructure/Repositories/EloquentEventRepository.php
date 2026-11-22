<?php

namespace App\Modules\Event\Infrastructure\Repositories;

use App\Modules\Event\Domain\Contracts\EventRepository;
use App\Modules\Event\Infrastructure\Models\Event;
use Illuminate\Database\Eloquent\Builder;

final class EloquentEventRepository implements EventRepository
{
    /**
     * Mengambil event berstatus published serta filter dan pagination.
     */
    public function paginatePublished(
        int $page = 1,
        int $perPage = 12,
        array $filters = []
    ): array {
        $query = Event::query()->published();

        // Pencarian judul atau nama penyelenggara.
        $search = trim((string) ($filters['search'] ?? ''));

        if ($search !== '') {
            $pattern = '%' . mb_strtolower($search) . '%';

            $query->where(
                static function (Builder $builder) use ($pattern): void {
                    $builder
                        ->whereRaw('LOWER(title) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(organizer_name) LIKE ?', [$pattern]);
                }
            );
        }

        // Filter jenis event.
        $eventType = trim((string) ($filters['event_type'] ?? ''));

        if ($eventType !== '') {
            $query->where('event_type', $eventType);
        }

        // Filter metode pelaksanaan event.
        $deliveryMode = trim((string) ($filters['delivery_mode'] ?? ''));

        if ($deliveryMode !== '') {
            $query->where('delivery_mode', $deliveryMode);
        }

        $page = max(1, $page);
        $perPage = max(1, min($perPage, 60));

        // Urutkan berdasarkan jadwal, lalu ID untuk urutan stabil.
        $paginator = $query
            ->orderBy('starts_at')
            ->orderBy('id')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'items' => $paginator
                ->getCollection()
                ->map(static fn (Event $event): array => $event->toArray())
                ->values()
                ->all(),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
        ];
    }

    /**
     * Hanya mengembalikan detail event yang sudah published.
     */
    public function findPublishedBySlug(string $slug): ?array
    {
        $event = Event::query()
            ->published()
            ->where('slug', $slug)
            ->first();

        return $event?->toArray();
    }
}
