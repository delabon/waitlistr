<?php

declare(strict_types=1);

namespace App\Actions\WaitlistSignups;

use App\Http\Resources\WaitlistSignupResource;
use App\Models\WaitlistSignup;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class GetWaitlistSignupsAction
{
    public function __invoke(int $maxItemsPerPage = 10): AnonymousResourceCollection
    {
        $waitlistSignups = WaitlistSignup::query()->latest('id')->paginate($maxItemsPerPage);

        return WaitlistSignupResource::collection($waitlistSignups);
    }
}
