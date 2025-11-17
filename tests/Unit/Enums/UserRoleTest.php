<?php

declare(strict_types=1);

use App\Enums\UserRole;

test('to array', function () {
    $roles = UserRole::toArray();

    expect($roles)->toBe([
        'admin' => 'Admin',
        'user' => 'User',
    ]);
});
