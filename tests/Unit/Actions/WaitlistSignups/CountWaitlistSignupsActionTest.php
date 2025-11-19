<?php

declare(strict_types=1);

use App\Actions\WaitlistSignups\CountWaitlistSignupsAction;
use Database\Factories\WaitlistSignupFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

it('returns zero when no signups exist', function () {
    $action = new CountWaitlistSignupsAction();

    $count = $action();

    expect($count)->toBe(0);
});

it('returns correct count when signups exist', function () {
    WaitlistSignupFactory::times(5)->create();

    $action = new CountWaitlistSignupsAction();

    $count = $action();

    expect($count)->toBe(5);
});

it('caches the count result', function () {
    WaitlistSignupFactory::times(3)->create();

    $action = new CountWaitlistSignupsAction();

    $firstCount = $action();
    expect($firstCount)->toBe(3);

    WaitlistSignupFactory::times(2)->create();

    $secondCount = $action();
    expect($secondCount)->toBe(3);
});

it('returns fresh count after cache is cleared', function () {
    WaitlistSignupFactory::times(3)->create();

    $action = new CountWaitlistSignupsAction();
    $firstCount = $action();

    WaitlistSignupFactory::times(2)->create();

    Cache::forget('waitlistSignupsCount');

    $secondCount = $action();

    expect($firstCount)->toBe(3);
    expect($secondCount)->toBe(5);
});

it('handles large number of signups', function () {
    WaitlistSignupFactory::times(1000)->create();

    $action = new CountWaitlistSignupsAction();

    $count = $action();

    expect($count)->toBe(1000);
});

it('returns integer type', function () {
    WaitlistSignupFactory::times(5)->create();

    $action = new CountWaitlistSignupsAction();

    $count = $action();

    expect($count)->toBeInt();
});

it('caches with correct key name', function () {
    WaitlistSignupFactory::times(3)->create();

    $action = new CountWaitlistSignupsAction();
    $action();

    expect(Cache::has('waitlistSignupsCount'))->toBeTrue();
});

it('cache expires after one week', function () {
    Carbon::setTestNow(Carbon::now());
    WaitlistSignupFactory::times(2)->create();

    $action = new CountWaitlistSignupsAction();
    $action();

    expect(Cache::has('waitlistSignupsCount'))->toBeTrue();

    Carbon::setTestNow(Carbon::now()->addWeeks(2));

    expect(Cache::has('waitlistSignupsCount'))->toBeFalse();

    Carbon::setTestNow(null);
});
