<?php

declare(strict_types=1);

use App\Actions\WaitlistSignups\CountWaitlistSignupsAction;
use App\Http\Middleware\HandleInertiaRequests;
use Database\Factories\UserFactory;
use Database\Factories\WaitlistSignupFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

it('shares signups count as lazy prop', function () {
    $middleware = new HandleInertiaRequests(new CountWaitlistSignupsAction());
    $user = UserFactory::new()->create();
    $request = Request::create('/test');
    $request->setUserResolver(static fn () => $user);

    $shared = $middleware->share($request);

    expect($shared)->toHaveKey('signupsCount');
    expect($shared['signupsCount'])->toBeCallable();
});

it('signups count returns correct value when called', function () {
    WaitlistSignupFactory::new()->count(1001)->create();

    $middleware = new HandleInertiaRequests(new CountWaitlistSignupsAction());
    $user = UserFactory::new()->create();
    $request = Request::create('/test');
    $request->setUserResolver(static fn () => $user);

    $shared = $middleware->share($request);
    $signupsCount = $shared['signupsCount']();

    expect($signupsCount)->toBe('1K');
});

it('signups count returns zero when no signups exist', function () {
    $middleware = new HandleInertiaRequests(new CountWaitlistSignupsAction());
    $user = UserFactory::new()->create();
    $request = Request::create('/test');
    $request->setUserResolver(static fn () => $user);

    $shared = $middleware->share($request);
    $signupsCount = $shared['signupsCount']();

    expect($signupsCount)->toBe('0');
});
