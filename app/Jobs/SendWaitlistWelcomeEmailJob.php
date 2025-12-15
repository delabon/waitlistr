<?php

declare(strict_types=1);

namespace App\Jobs;

use App\DTOs\WaitlistSignups\WaitlistSignupDTO;
use App\Mail\WaitlistSignupWelcomeMail;
use App\Models\WaitlistSignup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

final class SendWaitlistWelcomeEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly WaitlistSignup $waitlistSignup
    ) {}

    public function handle(): void
    {
        $dto = WaitlistSignupDTO::fromModel($this->waitlistSignup);

        Mail::to($dto->email)
            ->send(new WaitlistSignupWelcomeMail($dto));

        $this->waitlistSignup->update([
            'welcome_email_sent_at' => now(),
        ]);
    }
}
