<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\WaitlistSignup;
use Illuminate\Queue\SerializesModels;

final class WaitlistSignupCreated
{
    use SerializesModels;

    public function __construct(
        public readonly WaitlistSignup $waitlistSignup,
    ) {}
}
