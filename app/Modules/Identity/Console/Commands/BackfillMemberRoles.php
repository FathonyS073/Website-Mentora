<?php

declare(strict_types=1);

namespace App\Modules\Identity\Console\Commands;

use App\Models\User;
use App\Modules\Identity\Application\Services\DefaultMemberRoleAssigner;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

/**
 * Explicit, safe maintenance tool for accounts created before RBAC existed.
 * No account is changed without --apply and an explicit scope.
 */
final class BackfillMemberRoles extends Command
{
    protected $signature = 'mentora:rbac:backfill-members
        {--user-id= : Restrict to a specific existing user ID}
        {--all : Select all users currently without a role}
        {--apply : Perform assignment; without this flag it is only a preview}';

    protected $description = 'Preview or assign member to pre-RBAC accounts without existing roles';

    public function handle(DefaultMemberRoleAssigner $assigner): int
    {
        $all = (bool) $this->option('all');
        $userIdOption = $this->option('user-id');
        $hasUserId = is_string($userIdOption) && $userIdOption !== '';

        if ($all === $hasUserId) {
            $this->error('Choose exactly one scope: --all OR --user-id=<id>.');

            return self::FAILURE;
        }

        $userId = null;

        if ($hasUserId) {
            if (! ctype_digit($userIdOption) || (int) $userIdOption < 1) {
                $this->error('--user-id must be a positive integer.');

                return self::FAILURE;
            }

            $userId = (int) $userIdOption;
        }

        $query = $this->unassignedQuery($userId);
        $count = (clone $query)->count();

        $this->info("Eligible unassigned accounts: {$count}.");

        if (! $this->option('apply')) {
            $this->line('PREVIEW ONLY. No database records changed. Add --apply to perform the selected scope.');

            return self::SUCCESS;
        }

        $assigned = 0;

        $query->chunkById(100, static function ($users) use ($assigner, &$assigned): void {
            foreach ($users as $user) {
                if ($assigner->assignIfUnassigned($user)) {
                    $assigned++;
                }
            }
        });

        $this->info("New member assignments: {$assigned}.");

        return self::SUCCESS;
    }

    /** @return Builder<User> */
    private function unassignedQuery(?int $userId): Builder
    {
        $query = User::query()->whereNotIn('id', static function ($query): void {
            $query->select('user_id')->from('user_roles');
        });

        if ($userId !== null) {
            $query->whereKey($userId);
        }

        return $query;
    }
}
