<?php

declare(strict_types=1);

namespace App\Modules\Identity\Infrastructure\Persistence;

use App\Models\User;
use App\Modules\Identity\Domain\Contracts\AccountReader;
use App\Modules\Identity\Domain\ValueObjects\AccountSummary;

final class EloquentAccountReader implements AccountReader
{
    public function findById(int|string $userId): ?AccountSummary
    {
        $user = User::query()->find($userId);

        if ($user === null) {
            return null;
        }

        return new AccountSummary(
            id: (string) $user->getKey(),
            name: (string) $user->name,
            email: (string) $user->email,
        );
    }
}
