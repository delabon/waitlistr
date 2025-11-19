<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WaitlistSignup>
 */
final class WaitlistSignupFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->email(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'welcome_email_sent_at' => null,
        ];
    }

    public function welcomeEmailSentAt(?Carbon $date = null): self
    {
        return $this->state(fn (array $attributes) => [
            'welcome_email_sent_at' => $date ?? $this->faker->dateTimeBetween('-2 years'),
        ]);
    }
}
