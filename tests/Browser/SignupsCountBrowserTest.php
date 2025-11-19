<?php

declare(strict_types=1);

use Database\Factories\UserFactory;
use Database\Factories\WaitlistSignupFactory;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('admin can see signups count in sidebar navigation', function () {
    $adminUser = UserFactory::new()->admin()->create(['email' => 'admin@example.com']);
    WaitlistSignupFactory::new()->count(1001)->create();

    browserLogin($adminUser);
    $page = visit('/dashboard');

    $page->assertSeeIn('.signups-count', '1K');
});

test('signups count displays zero when no signups exist', function () {
    $adminUser = UserFactory::new()->admin()->create();

    browserLogin($adminUser);
    $page = visit('/dashboard');

    $page->assertSeeIn('.signups-count', '0');
});

test('signups count updates when new signups are added', function () {
    $adminUser = UserFactory::new()->admin()->create();
    WaitlistSignupFactory::new()->count(3)->create();

    browserLogin($adminUser);
    $page = visit('/dashboard');

    $page->assertSeeIn('.signups-count', '3');

    WaitlistSignupFactory::new()->count(2)->create();
    Cache::forget('waitlistSignupsCount');

    $page = visit('/dashboard');

    $page->assertSeeIn('.signups-count', '5');
});

test('signups count is visible on dashboard page', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->count(12)->create();

    browserLogin($adminUser);
    $page = visit('/dashboard');

    $page->assertSee('Signups')
        ->assertSee('12');
});

test('regular user does not see signups count', function () {
    $regularUser = UserFactory::new()->user()->create();
    WaitlistSignupFactory::new()->count(5)->create();

    browserLogin($regularUser);
    $page = visit('/dashboard');

    $content = $page->content();

    expect(str_contains($content, 'Signups'))->toBeFalse();
});

test('signups count persists across page navigation', function () {
    $adminUser = UserFactory::new()->admin()->create();
    WaitlistSignupFactory::new()->count(8)->create();

    browserLogin($adminUser);

    $dashboardPage = visit('/dashboard');
    $dashboardPage->assertSeeIn('.signups-count', '8');

    $signupsPage = visit('/dashboard/admin/signups');
    $signupsPage->assertSeeIn('.signups-count', '8');

    $backToDashboard = visit('/dashboard');
    $backToDashboard->assertSeeIn('.signups-count', '8');
});

test('signups count works on mobile viewport when menu is clicked', function () {
    $adminUser = UserFactory::new()->admin()->create();
    WaitlistSignupFactory::new()->count(3)->create();

    browserLogin($adminUser);
    $page = visit('/dashboard')->on()->mobile();

    $page->click('[data-slot="sidebar-trigger"]')
        ->wait(2)
        ->assertSeeIn('.signups-count', '3');
});

test('signups count works on desktop viewport', function () {
    $adminUser = UserFactory::new()->admin()->create();
    WaitlistSignupFactory::new()->count(3)->create();

    browserLogin($adminUser);
    $page = visit('/dashboard')->on()->desktop();

    $page->assertSeeIn('.signups-count', '3');
});
