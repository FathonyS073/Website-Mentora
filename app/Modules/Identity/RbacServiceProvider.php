<?php

declare(strict_types=1);

namespace App\Modules\Identity;

use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use App\Modules\Identity\Infrastructure\Persistence\DatabaseRbacAccessReader;
use Illuminate\Support\ServiceProvider;

/** Separate registration; the existing IdentityServiceProvider stays unchanged. */
final class RbacServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RbacAccessReader::class, DatabaseRbacAccessReader::class);
    }
}
