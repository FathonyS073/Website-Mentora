<?php

use Illuminate\Support\Facades\Route;

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
}