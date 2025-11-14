<?php

declare(strict_types=1);

use App\Models\WaitlistSignup;

test('to array', function () {
    $waitlistSignup = WaitlistSignup::factory()->create();

    expect($waitlistSignup->refresh()->toArray())->toHaveKeys([
        'email',
        'first_name',
        'last_name',
        'welcome_email_sent_at',
        'created_at',
        'updated_at',
    ]);
});
