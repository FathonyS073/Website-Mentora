<?php

declare(strict_types=1);

namespace App\Modules\Identity;

use App\Modules\Identity\Application\Listeners\AssignMemberOnRegistration;
use App\Modules\Identity\Console\Commands\BackfillMemberRoles;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/** Registers a single post-registration listener; keeps Fortify untouched. */
final class RegistrationRoleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(Registered::class, AssignMemberOnRegistration::class);

        if ($this->app->runningInConsole()) {
            $this->commands([BackfillMemberRoles::class]);
        }
    }
}
