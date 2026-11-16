<?php

declare(strict_types=1);

namespace App\Modules\Identity\Domain\ValueObjects;

final readonly class AccountSummary
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {}
}
