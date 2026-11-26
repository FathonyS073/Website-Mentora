<?php

declare(strict_types=1);

namespace App\Modules\Identity\Application\Listeners;

use App\Models\User;
use App\Modules\Identity\Application\Services\DefaultMemberRoleAssigner;
use Illuminate\Auth\Events\Registered;

/** Public registration cannot choose a role from request data. */
final class AssignMemberOnRegistration
{
    public function __construct(private readonly DefaultMemberRoleAssigner $assigner)
    {
    }

    public function handle(Registered $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $this->assigner->assignIfUnassigned($event->user);
    }
}
