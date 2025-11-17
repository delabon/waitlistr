<?php

declare(strict_types=1);

use Database\Factories\UserFactory;
use Database\Factories\WaitlistSignupFactory;
use Inertia\Testing\AssertableInertia;

test('admin can access waitlist signups page', function () {
    $adminUser = UserFactory::new()->admin()->create([
        'email' => 'admin@example.com',
    ]);

    $response = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
    );
});

test('non-admin user cannot access waitlist signups page', function () {
    $regularUser = UserFactory::new()->user()->create([
        'email' => 'user@example.com',
    ]);

    $response = $this->actingAs($regularUser)->get(route('dashboard.admin.signups'));

    $response->assertForbidden();
});

test('guest cannot access waitlist signups page', function () {
    $response = $this->get(route('dashboard.admin.signups'));

    $response->assertRedirect(route('login'));
});

test('admin can see paginated waitlist signups', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->count(15)->create();

    $response = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->has('waitlistSignups.data', 10)
        ->has('waitlistSignups.meta')
        ->where('waitlistSignups.meta.total', 15)
    );
});

test('waitlist signups are ordered by latest first', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'oldest@example.com',
        'created_at' => now()->subDays(3),
    ]);

    WaitlistSignupFactory::new()->create([
        'email' => 'middle@example.com',
        'created_at' => now()->subDays(2),
    ]);

    WaitlistSignupFactory::new()->create([
        'email' => 'newest@example.com',
        'created_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->where('waitlistSignups.data.0.email', 'newest@example.com')
        ->where('waitlistSignups.data.1.email', 'middle@example.com')
        ->where('waitlistSignups.data.2.email', 'oldest@example.com')
    );
});

test('admin can see signup details including email and names', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'john.doe@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $response = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->where('waitlistSignups.data.0.email', 'john.doe@example.com')
        ->where('waitlistSignups.data.0.first_name', 'John')
        ->where('waitlistSignups.data.0.last_name', 'Doe')
    );
});

test('admin can see signups with null first and last names', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'email-only@example.com',
        'first_name' => null,
        'last_name' => null,
    ]);

    $response = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->where('waitlistSignups.data.0.email', 'email-only@example.com')
        ->where('waitlistSignups.data.0.first_name', null)
        ->where('waitlistSignups.data.0.last_name', null)
    );
});

test('admin can see formatted creation date', function () {
    $adminUser = UserFactory::new()->admin()->create();

    $signup = WaitlistSignupFactory::new()->create();

    $response = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->where('waitlistSignups.data.0.id', $signup->id)
        ->where('waitlistSignups.data.0.formatted_created_at', $signup->formatted_created_at)
    );

    expect($signup->formatted_created_at)->toBeString()
        ->and($signup->formatted_created_at)->not->toBeEmpty();
});

test('admin can see formatted welcome email sent at date when email was sent', function () {
    $adminUser = UserFactory::new()->admin()->create();

    $signup = WaitlistSignupFactory::new()->create([
        'welcome_email_sent_at' => now()->subHours(2),
    ]);

    $response = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->where('waitlistSignups.data.0.id', $signup->id)
        ->where('waitlistSignups.data.0.formatted_welcome_email_sent_at', $signup->formatted_welcome_email_sent_at)
    );

    expect($signup->formatted_welcome_email_sent_at)->toBeString()
        ->and($signup->formatted_welcome_email_sent_at)->not->toBeEmpty();
});

test('admin can see empty string for welcome email sent at when email was not sent', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'welcome_email_sent_at' => null,
    ]);

    $response = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->where('waitlistSignups.data.0.formatted_welcome_email_sent_at', '')
    );
});

test('admin page displays empty state when no signups exist', function () {
    $adminUser = UserFactory::new()->admin()->create();

    $response = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->has('waitlistSignups.data', 0)
        ->where('waitlistSignups.meta.total', 0)
    );
});

test('pagination works correctly for admin signups page', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->count(25)->create();

    $firstPageResponse = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));
    $firstPageResponse->assertOk();
    $firstPageResponse->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->has('waitlistSignups.data', 10)
        ->where('waitlistSignups.meta.current_page', 1)
        ->where('waitlistSignups.meta.last_page', 3)
        ->where('waitlistSignups.meta.total', 25)
    );

    $secondPageResponse = $this->actingAs($adminUser)->get(route('dashboard.admin.signups', ['page' => 2]));
    $secondPageResponse->assertOk();
    $secondPageResponse->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->has('waitlistSignups.data', 10)
        ->where('waitlistSignups.meta.current_page', 2)
    );

    $thirdPageResponse = $this->actingAs($adminUser)->get(route('dashboard.admin.signups', ['page' => 3]));
    $thirdPageResponse->assertOk();
    $thirdPageResponse->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->has('waitlistSignups.data', 5)
        ->where('waitlistSignups.meta.current_page', 3)
    );
});

test('unverified admin can access waitlist signups page if email verification is not enforced', function () {
    $unverifiedAdmin = UserFactory::new()->admin()->unverified()->create();

    $response = $this->actingAs($unverifiedAdmin)->get(route('dashboard.admin.signups'));

    $response->assertOk();
});

test('verified admin can access waitlist signups page', function () {
    $verifiedAdmin = UserFactory::new()->admin()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($verifiedAdmin)->get(route('dashboard.admin.signups'));

    $response->assertOk();
});

test('admin page includes all required resource fields', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'complete@example.com',
        'first_name' => 'Complete',
        'last_name' => 'Record',
    ]);

    $response = $this->actingAs($adminUser)->get(route('dashboard.admin.signups'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('dashboard/admin/Signups')
        ->has('waitlistSignups.data.0.id')
        ->has('waitlistSignups.data.0.email')
        ->has('waitlistSignups.data.0.first_name')
        ->has('waitlistSignups.data.0.last_name')
        ->has('waitlistSignups.data.0.formatted_created_at')
        ->has('waitlistSignups.data.0.formatted_welcome_email_sent_at')
    );
});

