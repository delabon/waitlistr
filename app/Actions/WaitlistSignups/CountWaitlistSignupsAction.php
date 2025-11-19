<?php

declare(strict_types=1);

namespace App\Actions\WaitlistSignups;

use App\Models\WaitlistSignup;
use Illuminate\Support\Facades\Cache;

final class CountWaitlistSignupsAction
{
    public function __invoke(): int
    {
        /** @phpstan-ignore return.type */
        return (int) Cache::remember(
            'waitlistSignupsCount',
            now()->addWeek(),
            static fn () => WaitlistSignup::query()->count()
        );
    }
}
