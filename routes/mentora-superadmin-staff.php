<?php

declare(strict_types=1);

use App\Modules\Identity\Presentation\Http\Controllers\StaffInvitationsController;
use Illuminate\Support\Facades\Route;

// Required by routes/web.php outside all APP_ENV-local preview conditions.
// Independent role and permission checks; no administrative public registration.
Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth', 'verified', 'rbac.role:superadmin', 'rbac.permission:roles.assign'])
    ->group(function (): void {
        Route::get('/users', [StaffInvitationsController::class, 'index'])
            ->name('users.index');

        Route::post('/staff-invitations', [StaffInvitationsController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('staff-invitations.store');

        Route::post('/staff-invitations/{user}/resend', [StaffInvitationsController::class, 'resend'])
            ->middleware('throttle:5,1')
            ->name('staff-invitations.resend');
    });
