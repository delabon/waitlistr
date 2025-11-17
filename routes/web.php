<?php

declare(strict_types=1);

use App\Http\Controllers\WaitlistSignupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WaitlistSignupController::class, 'create'])
    ->middleware(['guest'])
    ->name('waitlistSignups.create');
Route::post('/waitlist-signups', [WaitlistSignupController::class, 'store'])
    ->name('waitlistSignups.store');

Route::prefix('/dashboard')->group(function () {
    Route::inertia('/', 'dashboard/Dashboard')
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::get('/admin/signups', [WaitlistSignupController::class, 'index'])
        ->middleware(['auth', 'verified', 'admin'])
        ->name('dashboard.admin.signups');
});

require __DIR__.'/settings.php';
