<?php

declare(strict_types=1);

use App\Actions\WaitlistSignups\GetWaitlistSignupsAction;
use Database\Factories\WaitlistSignupFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

it('returns paginated signups ordered by latest id', function () {
    $firstSignup = WaitlistSignupFactory::new()->create(['email' => 'first@example.com']);
    $secondSignup = WaitlistSignupFactory::new()->create(['email' => 'second@example.com']);
    $thirdSignup = WaitlistSignupFactory::new()->create(['email' => 'third@example.com']);

    $action = new GetWaitlistSignupsAction();

    $result = $action();

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->items())->toHaveCount(3);
    expect($result->items()[0]->id)->toBe($thirdSignup->id);
    expect($result->items()[1]->id)->toBe($secondSignup->id);
    expect($result->items()[2]->id)->toBe($firstSignup->id);
});

it('paginates with default 10 items per page', function () {
    WaitlistSignupFactory::times(15)->create();

    $action = new GetWaitlistSignupsAction();

    $result = $action();

    expect($result->perPage())->toBe(10);
    expect($result->items())->toHaveCount(10);
    expect($result->total())->toBe(15);
    expect($result->lastPage())->toBe(2);
});

it('accepts custom items per page parameter', function () {
    WaitlistSignupFactory::times(25)->create();

    $action = new GetWaitlistSignupsAction();

    $result = $action(maxItemsPerPage: 5);

    expect($result->perPage())->toBe(5);
    expect($result->items())->toHaveCount(5);
    expect($result->total())->toBe(25);
    expect($result->lastPage())->toBe(5);
});

it('returns empty paginator when no signups exist', function () {
    $action = new GetWaitlistSignupsAction();

    $result = $action();

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->items())->toHaveCount(0);
    expect($result->total())->toBe(0);
});

it('handles single signup correctly', function () {
    $onlySignup = WaitlistSignupFactory::new()->create(['email' => 'only@example.com']);

    $action = new GetWaitlistSignupsAction();

    $result = $action();

    expect($result->items())->toHaveCount(1);
    expect($result->items()[0]->id)->toBe($onlySignup->id);
    expect($result->items()[0]->email)->toBe('only@example.com');
});

it('returns correct pagination metadata', function () {
    WaitlistSignupFactory::times(35)->create();

    $action = new GetWaitlistSignupsAction();

    $result = $action(maxItemsPerPage: 10);

    expect($result->currentPage())->toBe(1);
    expect($result->hasMorePages())->toBeTrue();
    expect($result->lastPage())->toBe(4);
    expect($result->total())->toBe(35);
});
