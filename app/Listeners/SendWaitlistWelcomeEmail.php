<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\WaitlistSignupCreated;
use App\Jobs\SendWaitlistWelcomeEmailJob;

final class SendWaitlistWelcomeEmail
{
    public function handle(WaitlistSignupCreated $event): void
    {
        SendWaitlistWelcomeEmailJob::dispatch($event->waitlistSignup);
    }
}
