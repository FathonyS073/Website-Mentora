<?php

declare(strict_types=1);

namespace App\Modules\Identity\Presentation\Http\Middleware;

use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use App\Modules\Identity\Domain\Permissions\RbacPermissionMatrix;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Role-level entitlement only; resource ownership still requires a policy. */
final readonly class RequireRole
{
    public function __construct(private RbacAccessReader $access) {}

    public function handle(Request $request, Closure $next, string $roleCode = ''): Response
    {
        $user = $request->user();

        if ($user === null) {
            throw new AuthenticationException('Unauthenticated.', ['web']);
        }

        $userId = $user->getAuthIdentifier();

        if (
            ! is_int($userId) && ! is_string($userId)
            || ! array_key_exists($roleCode, RbacPermissionMatrix::grants())
            || ! $this->access->hasRole($userId, $roleCode)
        ) {
            abort(403);
        }

        return $next($request);
    }
}
