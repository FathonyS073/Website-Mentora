<?php

namespace App\Modules\Event;

use App\Modules\Event\Domain\Contracts\EventRepository;
use App\Modules\Event\Infrastructure\Repositories\EloquentEventRepository;
use Illuminate\Support\ServiceProvider;

final class EventServiceProvider extends ServiceProvider
{
    /**
     * Menghubungkan kontrak Domain ke implementasi Infrastructure.
     */
    public function register(): void
    {
        $this->app->bind(
            EventRepository::class,
            EloquentEventRepository::class
        );
    }
}
