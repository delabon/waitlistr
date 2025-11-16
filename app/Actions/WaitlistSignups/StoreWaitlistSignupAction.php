<?php

declare(strict_types=1);

namespace App\Actions\WaitlistSignups;

use App\DTOs\WaitlistSignups\WaitlistSignupDTO;
use App\Mail\WaitlistSignupWelcomeMail;
use App\Models\WaitlistSignup;
use Illuminate\Support\Facades\Mail;

final class StoreWaitlistSignupAction
{
    public function __invoke(WaitlistSignupDTO $dto): WaitlistSignup
    {
        $waitlistSignup = WaitlistSignup::create($dto->toArray());

        Mail::to($dto->email)
            ->queue(new WaitlistSignupWelcomeMail($dto));

        return $waitlistSignup;
    }
}
