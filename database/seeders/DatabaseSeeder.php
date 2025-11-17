<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        UserFactory::new()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => '12345678',
        ]);

        UserFactory::new()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => '12345678',
            'role' => UserRole::Admin,
        ]);
    }
}
