<?php

declare(strict_types=1);

use Database\Factories\UserFactory;

test('to array', function () {
    $user = UserFactory::new()->create();

    expect($user->refresh()->toArray())->toHaveKeys([
        'id',
        'name',
        'email',
        'email_verified_at',
        'two_factor_confirmed_at',
        'created_at',
        'updated_at',
        'role',
    ]);
});

test('is admin return true when admin', function () {
    $user = UserFactory::new()->admin()->make(); // No need for DB insert here

    expect($user->isAdmin())->toBeTrue();
});

test('is admin return false when regular user', function () {
    $user = UserFactory::new()->user()->make(); // No need for DB insert here

    expect($user->isAdmin())->toBeFalse();
});
