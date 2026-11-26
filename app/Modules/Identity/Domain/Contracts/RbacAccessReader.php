<?php

declare(strict_types=1);

namespace App\Modules\Identity\Domain\Contracts;

/** Reads existing role grants. Never provisions accounts or assigns privileges. */
interface RbacAccessReader
{
    /** @return list<string> */
    public function rolesFor(int|string $userId): array;

    /** @return list<string> */
    public function permissionsFor(int|string $userId): array;

    public function hasRole(int|string $userId, string $roleCode): bool;

    public function hasPermission(int|string $userId, string $permissionCode): bool;
}
