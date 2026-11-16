<?php

declare(strict_types=1);

namespace App\Modules\Identity\Domain\Contracts;

use App\Modules\Identity\Domain\ValueObjects\AccountSummary;

interface AccountReader
{
    public function findById(int|string $userId): ?AccountSummary;
}
