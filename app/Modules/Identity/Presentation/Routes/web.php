<?php

declare(strict_types=1);

use App\Modules\Identity\Presentation\Http\Controllers\AccountSummaryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('member')
    ->name('member.')
    ->group(function (): void {
        Route::get('account/summary', AccountSummaryController::class)
            ->name('account.summary');
    });
