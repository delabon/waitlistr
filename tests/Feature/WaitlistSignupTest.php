<?php

declare(strict_types=1);

use App\Mail\WaitlistSignupWelcomeMail;
use App\Models\User;
use App\Models\WaitlistSignup;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia;

test('waitlist signup page can be rendered', function () {
    $response = $this->get(route('waitlistSignups.create'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('Welcome'));
});

test('guest can signup for waitlist with all fields', function () {
    Mail::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    $response->assertRedirectBack();
    $response->assertRedirect(route('waitlistSignups.create'));

    $this->assertDatabaseHas('waitlist_signups', [
        'email' => 'john.doe@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $signup = WaitlistSignup::where('email', 'john.doe@example.com')->first();
    expect($signup)->not->toBeNull();
    expect($signup->email)->toBe('john.doe@example.com');
    expect($signup->first_name)->toBe('John');
    expect($signup->last_name)->toBe('Doe');
    expect($signup->welcome_email_sent_at)->toBeNull();

    Mail::assertQueued(WaitlistSignupWelcomeMail::class, function ($mail) {
        return $mail->dto->email === 'john.doe@example.com'
            && $mail->dto->firstName === 'John'
            && $mail->dto->lastName === 'Doe';
    });
});

test('guest can signup for waitlist with only email', function () {
    Mail::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'email' => 'minimal@example.com',
    ]);

    $response->assertStatus(302);

    $this->assertDatabaseHas('waitlist_signups', [
        'email' => 'minimal@example.com',
        'first_name' => null,
        'last_name' => null,
    ]);

    Mail::assertQueued(WaitlistSignupWelcomeMail::class, function ($mail) {
        return $mail->dto->email === 'minimal@example.com'
            && $mail->dto->firstName === null
            && $mail->dto->lastName === null;
    });
});

test('email is required for waitlist signup', function () {
    Mail::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'firstName' => 'John',
        'lastName' => 'Doe',
    ]);

    $response->assertSessionHasErrors(['email']);

    $this->assertDatabaseMissing('waitlist_signups', [
        'first_name' => 'John',
    ]);

    Mail::assertNothingQueued();
});

test('email must be valid format', function () {
    Mail::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['email']);

    $this->assertDatabaseMissing('waitlist_signups', [
        'email' => 'not-an-email',
    ]);

    Mail::assertNothingQueued();
});

test('email must be unique', function () {
    Mail::fake();

    WaitlistSignup::factory()->create([
        'email' => 'existing@example.com',
    ]);

    $response = $this->post(route('waitlistSignups.store'), [
        'email' => 'existing@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);

    expect(WaitlistSignup::where('email', 'existing@example.com')->count())->toBe(1);

    Mail::assertNothingQueued();
});

test('first name must be at least 2 characters if provided', function () {
    Mail::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'firstName' => 'J',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['firstName']);

    $this->assertDatabaseMissing('waitlist_signups', [
        'email' => 'test@example.com',
    ]);

    Mail::assertNothingQueued();
});

test('last name must be at least 2 characters if provided', function () {
    Mail::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'lastName' => 'D',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['lastName']);

    $this->assertDatabaseMissing('waitlist_signups', [
        'email' => 'test@example.com',
    ]);

    Mail::assertNothingQueued();
});

test('first name cannot exceed 255 characters', function () {
    Mail::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'firstName' => str_repeat('a', 256),
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['firstName']);

    Mail::assertNothingQueued();
});

test('last name cannot exceed 255 characters', function () {
    Mail::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'lastName' => str_repeat('a', 256),
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['lastName']);

    Mail::assertNothingQueued();
});

test('authenticated user can also signup for waitlist', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'authenticated@example.com',
    ]);

    $response = $this->actingAs($user)->post(route('waitlistSignups.store'), [
        'email' => 'waitlist@example.com',
        'firstName' => 'Authenticated',
        'lastName' => 'User',
    ]);

    $response->assertRedirectBack();

    $this->assertDatabaseHas('waitlist_signups', [
        'email' => 'waitlist@example.com',
        'first_name' => 'Authenticated',
        'last_name' => 'User',
    ]);

    Mail::assertQueued(WaitlistSignupWelcomeMail::class, function ($mail) {
        return $mail->dto->email === 'waitlist@example.com'
            && $mail->dto->firstName === 'Authenticated'
            && $mail->dto->lastName === 'User';
    });
});

test('multiple users can signup with different emails', function () {
    Mail::fake();

    $firstSignup = $this->post(route('waitlistSignups.store'), [
        'email' => 'first@example.com',
        'firstName' => 'First',
    ]);

    $secondSignup = $this->post(route('waitlistSignups.store'), [
        'email' => 'second@example.com',
        'firstName' => 'Second',
    ]);

    $firstSignup->assertRedirectBack();
    $secondSignup->assertRedirectBack();

    expect(WaitlistSignup::count())->toBe(2);
    $this->assertDatabaseHas('waitlist_signups', ['email' => 'first@example.com']);
    $this->assertDatabaseHas('waitlist_signups', ['email' => 'second@example.com']);

    Mail::assertQueued(WaitlistSignupWelcomeMail::class, function ($mail) {
        return $mail->dto->email === 'first@example.com';
    });
    Mail::assertQueued(WaitlistSignupWelcomeMail::class, function ($mail) {
        return $mail->dto->email === 'second@example.com';
    });
});

