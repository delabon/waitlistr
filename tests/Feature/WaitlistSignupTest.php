<?php

declare(strict_types=1);

use App\Jobs\SendWaitlistWelcomeEmailJob;
use App\Mail\WaitlistSignupWelcomeMail;
use App\Models\WaitlistSignup;
use Database\Factories\UserFactory;
use Database\Factories\WaitlistSignupFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia;

test('waitlist signup page can be rendered', function () {
    $response = $this->get(route('waitlistSignups.create'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('Welcome'));
});

test('guest can signup for waitlist with all fields', function () {
    Queue::fake();

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

    Queue::assertPushed(SendWaitlistWelcomeEmailJob::class, function ($job) use ($signup) {
        return $job->waitlistSignup->id === $signup->id
            && $job->waitlistSignup->email === 'john.doe@example.com'
            && $job->waitlistSignup->first_name === 'John'
            && $job->waitlistSignup->last_name === 'Doe';
    });
});

test('guest can signup for waitlist with only email', function () {
    Queue::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'email' => 'minimal@example.com',
    ]);

    $response->assertStatus(302);

    $this->assertDatabaseHas('waitlist_signups', [
        'email' => 'minimal@example.com',
        'first_name' => null,
        'last_name' => null,
    ]);

    $signup = WaitlistSignup::where('email', 'minimal@example.com')->first();

    Queue::assertPushed(SendWaitlistWelcomeEmailJob::class, function ($job) use ($signup) {
        return $job->waitlistSignup->id === $signup->id
            && $job->waitlistSignup->email === 'minimal@example.com'
            && $job->waitlistSignup->first_name === null
            && $job->waitlistSignup->last_name === null;
    });
});

test('email is required for waitlist signup', function () {
    Queue::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'firstName' => 'John',
        'lastName' => 'Doe',
    ]);

    $response->assertSessionHasErrors(['email']);

    $this->assertDatabaseMissing('waitlist_signups', [
        'first_name' => 'John',
    ]);

    Queue::assertNothingPushed();
});

test('email must be valid format', function () {
    Queue::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['email']);

    $this->assertDatabaseMissing('waitlist_signups', [
        'email' => 'not-an-email',
    ]);

    Queue::assertNothingPushed();
});

test('email must be unique', function () {
    Queue::fake();

    WaitlistSignupFactory::new()->create([
        'email' => 'existing@example.com',
    ]);

    $response = $this->post(route('waitlistSignups.store'), [
        'email' => 'existing@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);

    expect(WaitlistSignup::where('email', 'existing@example.com')->count())->toBe(1);

    Queue::assertNothingPushed();
});

test('first name must be at least 2 characters if provided', function () {
    Queue::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'firstName' => 'J',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['firstName']);

    $this->assertDatabaseMissing('waitlist_signups', [
        'email' => 'test@example.com',
    ]);

    Queue::assertNothingPushed();
});

test('last name must be at least 2 characters if provided', function () {
    Queue::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'lastName' => 'D',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['lastName']);

    $this->assertDatabaseMissing('waitlist_signups', [
        'email' => 'test@example.com',
    ]);

    Queue::assertNothingPushed();
});

test('first name cannot exceed 255 characters', function () {
    Queue::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'firstName' => str_repeat('a', 256),
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['firstName']);

    Queue::assertNothingPushed();
});

test('last name cannot exceed 255 characters', function () {
    Queue::fake();

    $response = $this->post(route('waitlistSignups.store'), [
        'lastName' => str_repeat('a', 256),
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['lastName']);

    Queue::assertNothingPushed();
});

test('authenticated user can also signup for waitlist', function () {
    Queue::fake();

    $user = UserFactory::new()->create([
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

    $signup = WaitlistSignup::where('email', 'waitlist@example.com')->first();

    Queue::assertPushed(SendWaitlistWelcomeEmailJob::class, function ($job) use ($signup) {
        return $job->waitlistSignup->id === $signup->id
            && $job->waitlistSignup->email === 'waitlist@example.com'
            && $job->waitlistSignup->first_name === 'Authenticated'
            && $job->waitlistSignup->last_name === 'User';
    });
});

test('multiple users can signup with different emails', function () {
    Queue::fake();

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

    $firstSignupModel = WaitlistSignup::where('email', 'first@example.com')->first();
    $secondSignupModel = WaitlistSignup::where('email', 'second@example.com')->first();

    Queue::assertPushed(SendWaitlistWelcomeEmailJob::class, function ($job) use ($firstSignupModel) {
        return $job->waitlistSignup->id === $firstSignupModel->id;
    });
    Queue::assertPushed(SendWaitlistWelcomeEmailJob::class, function ($job) use ($secondSignupModel) {
        return $job->waitlistSignup->id === $secondSignupModel->id;
    });
});

test('welcome email job sends email and updates timestamp when executed', function () {
    Mail::fake();

    $signup = WaitlistSignupFactory::new()->create([
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'welcome_email_sent_at' => null,
    ]);
    $dto =

    expect($signup->welcome_email_sent_at)->toBeNull();

    $job = new SendWaitlistWelcomeEmailJob($signup);
    $job->handle();

    $signup->refresh();

    expect($signup->welcome_email_sent_at)->not->toBeNull();

    Mail::assertSent(WaitlistSignupWelcomeMail::class, function ($mail) {
        return $mail->dto->email === 'test@example.com'
            && $mail->dto->firstName === 'John'
            && $mail->dto->lastName === 'Doe';
    });
});
