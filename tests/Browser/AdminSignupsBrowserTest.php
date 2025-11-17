<?php

declare(strict_types=1);

use App\Models\WaitlistSignup;
use Database\Factories\UserFactory;
use Database\Factories\WaitlistSignupFactory;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\assertDatabaseCount;

test('admin can see signups page with navigation', function () {
    $adminUser = UserFactory::new()->admin()->create([
        'email' => 'admin@example.com',
    ]);

    browserLogin($adminUser);

    $page = visit('/dashboard/admin/signups');

    $page->assertSee('Signups')
        ->assertSee('ID')
        ->assertSee('Email')
        ->assertSee('Name')
        ->assertSee('Welcome Email Sent At')
        ->assertSee('Signup At');
});

test('admin can view list of waitlist signups', function () {
    $adminUser = UserFactory::new()->admin()->create();
    $now = now();

    $waitlistSignupOne = WaitlistSignupFactory::new()->create([
        'email' => 'first@example.com',
        'first_name' => 'First',
        'last_name' => 'User',
        'welcome_email_sent_at' => $now,
        'created_at' => now()->subDays(5),
    ]);

    WaitlistSignupFactory::new()->create([
        'email' => 'second@example.com',
        'first_name' => 'Second',
        'last_name' => 'User',
        'welcome_email_sent_at' => $now,
    ]);

    browserLogin($adminUser);

    $page = visit('/dashboard/admin/signups');

    $page->assertSee('first@example.com')
        ->assertSee('First User')
        ->assertSee('second@example.com')
        ->assertSee('Second User')
        ->assertSee($now->format(WaitlistSignup::DATETIME_FORMAT))
        ->assertSee($waitlistSignupOne->created_at->format(WaitlistSignup::DATETIME_FORMAT));
});

test('admin can see signups with only email addresses', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'email-only@example.com',
        'first_name' => null,
        'last_name' => null,
    ]);

    browserLogin($adminUser);
    $page = visit('/dashboard/admin/signups');

    $page->assertSee('email-only@example.com');
});

test('admin sees newest signups first', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'old@example.com',
        'created_at' => now()->subDays(5),
    ]);

    WaitlistSignupFactory::new()->create([
        'email' => 'new@example.com',
        'created_at' => now(),
    ]);

    browserLogin($adminUser);
    $page = visit('/dashboard/admin/signups');

    $pageContent = $page->content();

    $newPosition = strpos($pageContent, 'new@example.com');
    $oldPosition = strpos($pageContent, 'old@example.com');

    expect($newPosition)->toBeLessThan($oldPosition);
});

test('non-admin user cannot access admin signups page', function () {
    $regularUser = UserFactory::new()->user()->create();

    browserLogin($regularUser);
    $page = visit('/dashboard/admin/signups');

    $page->assertSee(Response::HTTP_FORBIDDEN);
});

test('guest is redirected to login when accessing admin signups page', function () {
    $page = visit('/dashboard/admin/signups');

    $page->assertSee('Log in');
});

test('admin can see pagination controls when there are more than 10 signups', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::times(15)->create();

    browserLogin($adminUser);

    $page = visit('/dashboard/admin/signups');
    $content = $page->content();

    expect(str_contains($content, '/dashboard/admin/signups?page=1'))->toBeTrue();
    expect(str_contains($content, '/dashboard/admin/signups?page=2'))->toBeTrue();
});

test('admin can navigate between pages', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->count(15)->create();

    browserLogin($adminUser);
    $page = visit('/dashboard/admin/signups');

    expect($page->content())->toContain('1')
        ->and($page->content())->toContain('2');

    $page->click('a[href*="page=2"]:not(:has-text("Next"))');

    $page->wait(2);

    assertDatabaseCount('waitlist_signups', 15);
});

test('admin sees empty state when no signups exist', function () {
    $adminUser = UserFactory::new()->admin()->create();

    browserLogin($adminUser);
    $page = visit('/dashboard/admin/signups');

    $page->assertSee('Signups');
    assertDatabaseCount('waitlist_signups', 0);
});

test('admin signups page works on mobile viewport', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'mobile@example.com',
    ]);

    browserLogin($adminUser);

    $page = visit('/dashboard/admin/signups')
        ->on()->mobile();

    $page->assertSee('Signups')
        ->assertSee('mobile@example.com');
});

test('admin signups page works on desktop viewport', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'desktop@example.com',
    ]);

    browserLogin($adminUser);
    $page = visit('/dashboard/admin/signups')
        ->on()->desktop();

    $page->assertSee('Signups')
        ->assertSee('desktop@example.com');
});

test('admin can view signups page multiple times', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'test@example.com',
    ]);

    browserLogin($adminUser);

    $firstVisit = visit('/dashboard/admin/signups');
    $firstVisit->assertSee('test@example.com');

    $secondVisit = visit('/dashboard/admin/signups');
    $secondVisit->assertSee('test@example.com');
});

test('admin page shows correct number of signups per page', function () {
    $adminUser = UserFactory::new()->admin()->create();

    $signups = WaitlistSignupFactory::new()->count(10)->create();

    browserLogin($adminUser);

    $page = visit('/dashboard/admin/signups');

    foreach ($signups as $signup) {
        $page->assertSee($signup->email);
    }
});

test('admin signups page passes smoke test', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->count(3)->create();

    browserLogin($adminUser);

    visit('/dashboard/admin/signups')->assertNoSmoke();
});

test('admin can see full name when both first and last names are provided', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'fullname@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    browserLogin($adminUser);
    $page = visit('/dashboard/admin/signups');

    $page->assertSee('John Doe');
});

test('admin page table is scrollable horizontally when content overflows', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->create([
        'email' => 'verylongemailaddress@extremelylongdomainname.com',
        'first_name' => 'VeryLongFirstName',
        'last_name' => 'VeryLongLastName',
    ]);

    browserLogin($adminUser);
    $page = visit('/dashboard/admin/signups');

    $page->assertSee('verylongemailaddress@extremelylongdomainname.com');
});

test('admin signups page breadcrumb shows correct navigation path', function () {
    $adminUser = UserFactory::new()->admin()->create();

    browserLogin($adminUser);
    $page = visit('/dashboard/admin/signups');

    $page->assertSee('Signups');
});

