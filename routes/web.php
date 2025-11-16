<?php

declare(strict_types=1);

use App\Http\Controllers\WaitlistSignupController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [WaitlistSignupController::class, 'create'])
    ->middleware(['guest'])
    ->name('waitlistSignups.create');
Route::post('/waitlist-signups', [WaitlistSignupController::class, 'store'])
    ->name('waitlistSignups.store');

// TODO: Move the starter kit code to a dedicated controller
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
