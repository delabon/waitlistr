<?php

declare(strict_types=1);

use App\Models\WaitlistSignup;
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

it('formats the created_at attribute', function () {
    $waitlistSignup = WaitlistSignupFactory::new()->create();

    expect($waitlistSignup->formatted_created_at)->toBe($waitlistSignup->created_at->format(WaitlistSignup::DATETIME_FORMAT));
});

it('returns empty string when trying to format the created_at attribute when is null', function () {
    $waitlistSignup = WaitlistSignupFactory::new()->make();

    expect($waitlistSignup->formatted_created_at)->toBeEmpty();
});

it('formats the welcome_email_sent_at attribute', function () {
    $waitlistSignup = WaitlistSignupFactory::new()->welcomeEmailSentAt(now())->create();

    expect($waitlistSignup->formatted_welcome_email_sent_at)->toBe($waitlistSignup->welcome_email_sent_at->format(WaitlistSignup::DATETIME_FORMAT));
});

it('returns empty string when trying to format the welcome_email_sent_at attribute when is null', function () {
    $waitlistSignup = WaitlistSignupFactory::new()->make();

    expect($waitlistSignup->formatted_welcome_email_sent_at)->toBeEmpty();
});
