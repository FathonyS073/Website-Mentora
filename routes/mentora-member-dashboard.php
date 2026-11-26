<?php

declare(strict_types=1);

use App\Modules\Identity\Presentation\Http\Controllers\MemberDashboardController;
use Illuminate\Support\Facades\Route;

// This file is required by routes/web.php (the web middleware group is inherited).
// 'verified' preserves the existing Fortify email-verification policy.
// The role check remains explicit even if a privileged user holds profile.view.
Route::middleware(['auth', 'verified', 'rbac.role:member', 'rbac.permission:profile.view'])
    ->get('/member/dashboard', MemberDashboardController::class)
    ->name('member.dashboard');
