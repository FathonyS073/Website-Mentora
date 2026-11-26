<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Modules\Identity\Application\Services\PublicRegistrationRoleAssigner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

/**
 * Fortify registration for the two public account types only.
 *
 * The legacy payload without account_type remains Member. A caller may not
 * choose any internal role, regardless of other injected request fields.
 */
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(
        private readonly PublicRegistrationRoleAssigner $roleAssigner,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'account_type' => ['sometimes', 'required', 'string', Rule::in(['member', 'guardian'])],
        ])->validate();

        // If an older client omits the new field, preserve the established
        // Member-only default. Only a validated value is ever read.
        $accountType = $input['account_type'] ?? 'member';

        // Create both account and role atomically. Fortify dispatches its
        // Registered event AFTER this action returns, so S2-01E will see
        // that the account already has its intended role and add nothing.
        return DB::transaction(function () use ($input, $accountType): User {
            $user = User::query()->create([
                'name' => $input['name'],
                'email' => $input['email'],
                // The User model already has the 'hashed' password cast.
                'password' => $input['password'],
            ]);

            $this->roleAssigner->assignToNewUser($user, $accountType);

            return $user;
        });
    }
}
