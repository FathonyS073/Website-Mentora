<?php

declare(strict_types=1);

namespace App\Modules\Identity\Domain\ValueObjects;

/** Snapshot of currently assigned roles and their effective permissions. */
final readonly class UserAccess
{
    /**
     * @param list<string> $roles
     * @param list<string> $permissions
     */
    public function __construct(
        public array $roles,
        public array $permissions,
    ) {}

    public function hasRole(string $roleCode): bool
    {
        return in_array($roleCode, $this->roles, true);
    }

    public function can(string $permissionCode): bool
    {
        return in_array($permissionCode, $this->permissions, true);
    }
}
