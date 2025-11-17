<?php

declare(strict_types=1);

use App\Http\Resources\WaitlistSignupResource;
use App\Models\WaitlistSignup;
use Database\Factories\WaitlistSignupFactory;

test('resource transforms waitlist signup model correctly', function () {
    $now = now();
    $nowFormatted = $now->format(WaitlistSignup::DATETIME_FORMAT);
    $signup = WaitlistSignupFactory::new()->welcomeEmailSentAt($now)->create([
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'welcome_email_sent_at' => $now,
        'created_at' => $now,
    ]);

    $resource = new WaitlistSignupResource($signup);
    $array = $resource->toArray(request());

    expect($array)->toBeArray()
        ->and($array)->toHaveKey('id')
        ->and($array)->toHaveKey('email')
        ->and($array)->toHaveKey('first_name')
        ->and($array)->toHaveKey('last_name')
        ->and($array)->toHaveKey('formatted_created_at')
        ->and($array)->toHaveKey('formatted_welcome_email_sent_at')
        ->and($array['id'])->toBe($signup->id)
        ->and($array['email'])->toBe('test@example.com')
        ->and($array['first_name'])->toBe('John')
        ->and($array['last_name'])->toBe('Doe')
        ->and($array['formatted_created_at'])->toBe($nowFormatted)
        ->and($array['formatted_welcome_email_sent_at'])->toBe($nowFormatted);
});

test('resource returns empty string for formatted welcome email sent at when email was not sent', function () {
    $signup = WaitlistSignupFactory::new()->create([
        'welcome_email_sent_at' => null,
    ]);

    $resource = new WaitlistSignupResource($signup);
    $array = $resource->toArray(request());

    expect($array['formatted_welcome_email_sent_at'])->toBe('');
});

test('resource handles null first and last names', function () {
    $signup = WaitlistSignupFactory::new()->create([
        'email' => 'email-only@example.com',
        'first_name' => null,
        'last_name' => null,
    ]);

    $resource = new WaitlistSignupResource($signup);
    $array = $resource->toArray(request());

    expect($array['first_name'])->toBeNull()
        ->and($array['last_name'])->toBeNull()
        ->and($array['email'])->toBe('email-only@example.com');
});

test('resource collection transforms multiple signups correctly', function () {
    $signups = WaitlistSignupFactory::new()->count(3)->create();

    $collection = WaitlistSignupResource::collection($signups);
    $array = $collection->toArray(request());

    expect($array)->toBeArray()
        ->and($array)->toHaveCount(3)
        ->and($array[0])->toHaveKey('id')
        ->and($array[0])->toHaveKey('email')
        ->and($array[1])->toHaveKey('id')
        ->and($array[1])->toHaveKey('email')
        ->and($array[2])->toHaveKey('id')
        ->and($array[2])->toHaveKey('email');
});
