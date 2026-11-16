<?php

declare(strict_types=1);

namespace App\Modules\Identity\Application\Queries;

use App\Modules\Identity\Domain\Contracts\AccountReader;
use App\Modules\Identity\Domain\ValueObjects\AccountSummary;

final readonly class GetAccountSummary
{
    public function __construct(private AccountReader $accounts) {}

    public function execute(int|string $userId): ?AccountSummary
    {
        return $this->accounts->findById($userId);
    }
}
