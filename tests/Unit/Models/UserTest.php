<?php

declare(strict_types=1);

use App\Models\User;

test('to array', function () {
    $user = User::factory()->create();

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
