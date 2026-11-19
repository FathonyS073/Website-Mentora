<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Scholarship\Presentation\Http\Controllers\ScholarshipCatalogController;
use App\Modules\Scholarship\Presentation\Http\Controllers\ScholarshipDetailController;


Route::inertia('/', 'Mentora/Home')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

// Pratinjau UI hanya untuk lingkungan development lokal.
if (app()->environment('local')) {
    Route::inertia(
        '/ui-preview/public',
        'Mentora/PublicLayoutPreview'
    )->name('ui-preview.public');
    Route::inertia(
    '/ui-preview/scholarship-cards',
    'Mentora/Scholarships/CardPreview'
    )->name('ui-preview.scholarship-cards');
}

Route::get('/beasiswa', ScholarshipCatalogController::class)
    ->name('scholarships.index');

    // Halaman detail beasiswa untuk pengunjung publik.
Route::get('/beasiswa/{slug}', ScholarshipDetailController::class)
    ->name('scholarships.show');