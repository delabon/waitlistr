<?php

declare(strict_types=1);

use Database\Factories\WaitlistSignupFactory;

test('to array', function () {
    $waitlistSignup = WaitlistSignupFactory::new()->create();

    expect($waitlistSignup->refresh()->toArray())->toHaveKeys([
        'email',
        'first_name',
        'last_name',
        'welcome_email_sent_at',
        'created_at',
        'updated_at',
    ]);
});
