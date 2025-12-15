<?php

declare(strict_types=1);

namespace App\DTOs\WaitlistSignups;

use App\Models\WaitlistSignup;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string|null>
 */
final readonly class WaitlistSignupDTO implements Arrayable
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public string $email
    ) {}

    /**
     * @param  array<string, string|null>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['firstName'] ?? $data['first_name'] ?? null,
            lastName: $data['lastName'] ?? $data['last_name'] ?? null,
            email: $data['email'] ?? ''
        );
    }

    public static function fromModel(WaitlistSignup $waitlistSignup): self
    {
        /** @phpstan-ignore argument.type */
        return self::fromArray($waitlistSignup->toArray());
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
        ];
    }
}
