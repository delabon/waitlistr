<?php

declare(strict_types=1);

namespace App\DTOs\WaitlistSignups;

use App\Http\Requests\StoreWaitlistSignupRequest;

final readonly class WaitlistSignupDTO
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public string $email
    ) {
    }

    public static function fromRequest(StoreWaitlistSignupRequest $request): self
    {
        return new self(
            firstName: $request->input('firstName'),
            lastName: $request->input('lastName'),
            email: $request->string('email')->toString()
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
        ];
    }
}
