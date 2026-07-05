<?php

declare(strict_types=1);

use App\Actions\WaitlistSignups\StoreWaitlistSignupAction;
use App\DTOs\WaitlistSignups\WaitlistSignupDTO;
use App\Events\WaitlistSignupCreated;
use App\Models\WaitlistSignup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Cache::flush();
    Event::fake();
});

it('creates waitlist signup from DTO', function () {
    $dto = WaitlistSignupDTO::fromArray([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com'
    ]);

    $action = new StoreWaitlistSignupAction();

    $result = $action->handle($dto);

    expect($result)->toBeInstanceOf(WaitlistSignup::class);
    expect($result->first_name)->toBe('John');
    expect($result->last_name)->toBe('Doe');
    expect($result->email)->toBe('john.doe@example.com');
    expect($result->id)->not->toBeNull();

    $this->assertDatabaseHas('waitlist_signups', [
        'id' => $result->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);
});

it('dispatches WaitlistSignupCreated event', function () {
    $dto = WaitlistSignupDTO::fromArray([
        'firstName' => 'Alice',
        'lastName' => 'Johnson',
        'email' => 'alice.johnson@example.com'
    ]);

    $action = new StoreWaitlistSignupAction();

    $result = $action->handle($dto);

    Event::assertDispatched(WaitlistSignupCreated::class, function ($event) use ($result) {
        return $event->waitlistSignup->id === $result->id
            && $event->waitlistSignup->email === 'alice.johnson@example.com';
    });
});

it('clears waitlistSignupsCount cache', function () {
    Cache::put('waitlistSignupsCount', 42);
    expect(Cache::has('waitlistSignupsCount'))->toBeTrue();

    $dto = WaitlistSignupDTO::fromArray([
        'firstName' => 'Bob',
        'lastName' => 'Wilson',
        'email' => 'bob.wilson@example.com'
    ]);

    $action = new StoreWaitlistSignupAction();

    $action->handle($dto);

    expect(Cache::has('waitlistSignupsCount'))->toBeFalse();
});

it('handles signup with only email and no names', function () {
    $dto = WaitlistSignupDTO::fromArray([
        'firstName' => null,
        'lastName' => null,
        'email' => 'noname@example.com'
    ]);

    $action = new StoreWaitlistSignupAction();

    $result = $action->handle($dto);

    expect($result->first_name)->toBeNull();
    expect($result->last_name)->toBeNull();
    expect($result->email)->toBe('noname@example.com');

    $this->assertDatabaseHas('waitlist_signups', [
        'id' => $result->id,
        'first_name' => null,
        'last_name' => null,
        'email' => 'noname@example.com',
    ]);
});

it('handles signup with only first name', function () {
    $dto = WaitlistSignupDTO::fromArray([
        'firstName' => 'Charlie',
        'lastName' => null,
        'email' => 'charlie@example.com'
    ]);

    $action = new StoreWaitlistSignupAction();

    $result = $action->handle($dto);

    expect($result->first_name)->toBe('Charlie');
    expect($result->last_name)->toBeNull();
    expect($result->email)->toBe('charlie@example.com');
});

it('returns created model with all attributes', function () {
    $dto = WaitlistSignupDTO::fromArray([
        'firstName' => 'David',
        'lastName' => 'Brown',
        'email' => 'david.brown@example.com'
    ]);

    $action = new StoreWaitlistSignupAction();

    $result = $action->handle($dto);

    expect($result)->toBeInstanceOf(WaitlistSignup::class);
    expect($result->exists)->toBeTrue();
    expect($result->wasRecentlyCreated)->toBeTrue();
    expect($result->created_at)->not->toBeNull();
    expect($result->updated_at)->not->toBeNull();
});
