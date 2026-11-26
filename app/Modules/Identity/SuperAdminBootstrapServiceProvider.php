<?php

declare(strict_types=1);

namespace App\Modules\Identity;

use App\Modules\Identity\Console\Commands\BootstrapFirstSuperAdmin;
use Illuminate\Support\ServiceProvider;

/** Register a privileged bootstrap command for Artisan only. No HTTP routes. */
final class SuperAdminBootstrapServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([BootstrapFirstSuperAdmin::class]);
        }
    }
}
