<?php

declare(strict_types=1);

namespace App\Modules\Identity;

use App\Modules\Identity\Domain\Contracts\RbacAccessReader;
use App\Modules\Identity\Domain\Permissions\RbacPermissionMatrix;
use App\Modules\Identity\Infrastructure\Persistence\DatabaseRbacAccessReader;
use App\Modules\Identity\Presentation\Http\Middleware\RequirePermission;
use App\Modules\Identity\Presentation\Http\Middleware\RequireRole;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Existing RBAC contract binding plus reusable route middleware and Gates.
 * Does not grant any role or change a route's middleware automatically.
 */
final class RbacServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RbacAccessReader::class, DatabaseRbacAccessReader::class);
    }

    public function boot(Router $router): void
    {
        // Aliases are registered with Laravel's router (no app.php replacement).
        $router->aliasMiddleware('rbac.role', RequireRole::class);
        $router->aliasMiddleware('rbac.permission', RequirePermission::class);

        // Do not use Gate::before: an unconditional SuperAdmin bypass would
        // undermine separation of duties and model-level policies.
        foreach (array_keys(RbacPermissionMatrix::permissions()) as $permissionCode) {
            if (Gate::has($permissionCode)) {
                continue; // Never overwrite an existing application's gate.
            }

            Gate::define($permissionCode, function (Authenticatable $user) use ($permissionCode): bool {
                $userId = $user->getAuthIdentifier();

                if (! is_int($userId) && ! is_string($userId)) {
                    return false;
                }

                return $this->app->make(RbacAccessReader::class)
                    ->hasPermission($userId, $permissionCode);
            });
        }
    }
}
