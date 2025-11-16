<?php

declare(strict_types=1);

use App\Models\WaitlistSignup;

use function Pest\Laravel\assertDatabaseHas;

test('guest can see waitlist signup form', function () {
    $page = visit('/');

    $page->assertSee('Join the waitlist!')
        ->assertSee('First Name')
        ->assertSee('Last Name')
        ->assertSee('Email')
        ->assertSee("Signup Now - We're Launching Soon!");
});

test('guest can successfully signup for waitlist with complete information', function () {
    $page = visit('/');

    $page->type('[name="firstName"]', 'Jane')
        ->type('[name="lastName"]', 'Smith')
        ->type('[name="email"]', 'jane.smith@example.com')
        ->press("Signup Now - We're Launching Soon!")
        ->wait(3)
        ->assertSee('You\'ve joined the list successfully!')
        ->assertSee('You should receive a welcome email shortly.');

    assertDatabaseHas('waitlist_signups', [
        'email' => 'jane.smith@example.com',
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ]);
});

test('guest can signup with only email address', function () {
    $page = visit('/');

    $page->type('[name="email"]', 'minimal@example.com')
        ->press("Signup Now - We're Launching Soon!")
        ->wait(3)
        ->assertSee('You\'ve joined the list successfully!');

    assertDatabaseHas('waitlist_signups', [
        'email' => 'minimal@example.com',
        'first_name' => null,
        'last_name' => null,
    ]);
});

test('multiple guests can signup sequentially', function () {
    $firstPage = visit('/');
    $firstPage->type('[name="email"]', 'first@example.com')
        ->press("Signup Now - We're Launching Soon!")
        ->wait(3)
        ->assertSee('You\'ve joined the list successfully!');

    $secondPage = visit('/');
    $secondPage->type('[name="email"]', 'second@example.com')
        ->press("Signup Now - We're Launching Soon!")
        ->wait(3)
        ->assertSee('You\'ve joined the list successfully!');

    expect(WaitlistSignup::count())->toBe(2);
});

test('form shows validation error for missing email', function () {
    $page = visit('/');

    $page->type('[name="firstName"]', 'John')
        ->type('[name="lastName"]', 'Doe')
        ->press("Signup Now - We're Launching Soon!")
        ->wait(3)
        ->assertSee('The email field is required.');

    expect(WaitlistSignup::count())->toBe(0);
});

test('form shows validation error for invalid email format', function () {
    $page = visit('/');

    $page->type('[name="email"]', 'not-a-valid-email')
        ->press("Signup Now - We're Launching Soon!")
        ->wait(1);

    expect(WaitlistSignup::count())->toBe(0);
});

test('form shows validation error for duplicate email', function () {
    WaitlistSignup::factory()->create([
        'email' => 'duplicate@example.com',
    ]);

    $page = visit('/');

    $page->type('[name="email"]', 'duplicate@example.com')
        ->press("Signup Now - We're Launching Soon!")
        ->wait(3)
        ->assertSee('The email has already been taken.');

    expect(WaitlistSignup::where('email', 'duplicate@example.com')->count())->toBe(1);
});

test('form clears after successful submission', function () {
    $page = visit('/');

    $page->type('[name="firstName"]', 'Test')
        ->type('[name="lastName"]', 'User')
        ->type('[name="email"]', 'test.user@example.com')
        ->press("Signup Now - We're Launching Soon!")
        ->wait(3)
        ->assertSee('You\'ve joined the list successfully!')
        ->assertDontSeeIn('[name="firstName"]', 'Test')
        ->assertDontSeeIn('[name="lastName"]', 'User')
        ->assertDontSeeIn('[name="email"]', 'test.user@example.com');

    assertDatabaseHas('waitlist_signups', [
        'email' => 'test.user@example.com',
        'first_name' => 'Test',
        'last_name' => 'User',
    ]);
});

test('button shows loading state during submission', function () {
    $page = visit('/');

    $page->type('[name="email"]', 'loading.test@example.com')
        ->press("Signup Now - We're Launching Soon!")
        ->assertSee('Joining...');
});

test('form works on mobile viewport', function () {
    $page = visit('/')
        ->on()->mobile();

    $page->assertSee('Join the waitlist!')
        ->type('[name="email"]', 'mobile@example.com')
        ->press("Signup Now - We're Launching Soon!")
        ->wait(3)
        ->assertSee('You\'ve joined the list successfully!');

    assertDatabaseHas('waitlist_signups', [
        'email' => 'mobile@example.com',
    ]);
});

test('form works on desktop viewport', function () {
    $page = visit('/')
        ->on()->desktop();

    $page->assertSee('Join the waitlist!')
        ->type('[name="email"]', 'desktop@example.com')
        ->press("Signup Now - We're Launching Soon!")
        ->wait(3)
        ->assertSee('You\'ve joined the list successfully!');

    assertDatabaseHas('waitlist_signups', [
        'email' => 'desktop@example.com',
    ]);
});

test('page passes smoke test (no JS errors and console logs)', function () {
    visit('/')->assertNoSmoke();
});
