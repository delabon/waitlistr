<?php

declare(strict_types=1);

namespace App\Actions\WaitlistSignups;

use App\DTOs\WaitlistSignups\WaitlistSignupDTO;
use App\Events\WaitlistSignupCreated;
use App\Models\WaitlistSignup;
use Illuminate\Support\Facades\Event;

final class StoreWaitlistSignupAction
{
    public function __invoke(WaitlistSignupDTO $dto): WaitlistSignup
    {
        $waitlistSignup = WaitlistSignup::create($dto->toArray());

        Event::dispatch(new WaitlistSignupCreated($waitlistSignup));

        return $waitlistSignup;
    }
}
