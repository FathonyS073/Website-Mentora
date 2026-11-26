<?php

declare(strict_types=1);

namespace App\Modules\Identity\Presentation\Http\Middleware;

use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use App\Modules\Identity\Domain\Permissions\RbacPermissionMatrix;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Explicit permission enforcement without SuperAdmin bypass. */
final readonly class RequirePermission
{
    public function __construct(private RbacAccessReader $access) {}

    public function handle(Request $request, Closure $next, string $permissionCode = ''): Response
    {
        $user = $request->user();

        if ($user === null) {
            throw new AuthenticationException('Unauthenticated.', ['web']);
        }

        $userId = $user->getAuthIdentifier();

        if (
            ! is_int($userId) && ! is_string($userId)
            || ! array_key_exists($permissionCode, RbacPermissionMatrix::permissions())
            || ! $this->access->hasPermission($userId, $permissionCode)
        ) {
            abort(403);
        }

        return $next($request);
    }
}
