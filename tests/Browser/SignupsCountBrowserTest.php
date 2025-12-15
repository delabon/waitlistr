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

    $this->actingAs($adminUser);

    visit('/dashboard')
        ->assertSeeIn('.signups-count', '1K');
});

test('signups count displays zero when no signups exist', function () {
    $adminUser = UserFactory::new()->admin()->create();

    $this->actingAs($adminUser);

    visit('/dashboard')
        ->assertSeeIn('.signups-count', '0');
});

test('signups count updates when new signups are added', function () {
    $adminUser = UserFactory::new()->admin()->create();
    WaitlistSignupFactory::new()->count(3)->create();

    $this->actingAs($adminUser);

    visit('/dashboard')
        ->assertSeeIn('.signups-count', '3');

    WaitlistSignupFactory::new()->count(2)->create();
    Cache::forget('waitlistSignupsCount');

    visit('/dashboard')
        ->assertSeeIn('.signups-count', '5');
});

test('signups count is visible on dashboard page', function () {
    $adminUser = UserFactory::new()->admin()->create();

    WaitlistSignupFactory::new()->count(12)->create();

    $this->actingAs($adminUser);

    visit('/dashboard')
        ->assertSee('Signups')
        ->assertSee('12');
});

test('regular user does not see signups count', function () {
    $regularUser = UserFactory::new()->user()->create();
    WaitlistSignupFactory::new()->count(5)->create();

    $this->actingAs($regularUser);

    visit('/dashboard')
        ->assertDontSee('Signups');
});

test('signups count persists across page navigation', function () {
    $adminUser = UserFactory::new()->admin()->create();
    WaitlistSignupFactory::new()->count(8)->create();

    $this->actingAs($adminUser);

    visit('/dashboard')
        ->assertSeeIn('.signups-count', '8');

    visit('/dashboard/admin/signups')
        ->assertSeeIn('.signups-count', '8');

    visit('/dashboard')
        ->assertSeeIn('.signups-count', '8');
});

test('signups count works on mobile viewport when menu is clicked', function () {
    $adminUser = UserFactory::new()->admin()->create();
    WaitlistSignupFactory::new()->count(3)->create();

    $this->actingAs($adminUser);

    visit('/dashboard')
        ->on()
        ->mobile()
        ->click('[data-slot="sidebar-trigger"]')
        ->wait(2)
        ->assertSeeIn('.signups-count', '3');
});

test('signups count works on desktop viewport', function () {
    $adminUser = UserFactory::new()->admin()->create();
    WaitlistSignupFactory::new()->count(3)->create();

    $this->actingAs($adminUser);

    visit('/dashboard')
        ->on()
        ->desktop()
        ->assertSeeIn('.signups-count', '3');
});
