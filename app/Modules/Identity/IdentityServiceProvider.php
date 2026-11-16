<?php

declare(strict_types=1);

namespace App\Modules\Identity;

use App\Modules\Identity\Domain\Contracts\AccountReader;
use App\Modules\Identity\Infrastructure\Persistence\EloquentAccountReader;
use Illuminate\Support\ServiceProvider;

final class IdentityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AccountReader::class, EloquentAccountReader::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/Presentation/Routes/web.php');
    }
}
