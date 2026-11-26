<?php

declare(strict_types=1);

namespace App\Modules\Identity\Application\Queries;

use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use App\Modules\Identity\Domain\ValueObjects\UserAccess;

final readonly class GetUserAccess
{
    public function __construct(private RbacAccessReader $access) {}

    public function execute(int|string $userId): UserAccess
    {
        return new UserAccess(
            roles: $this->access->rolesFor($userId),
            permissions: $this->access->permissionsFor($userId),
        );
    }
}
