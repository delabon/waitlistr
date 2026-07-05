<?php

declare(strict_types=1);

namespace App\Actions\WaitlistSignups;

use App\Models\WaitlistSignup;
use Illuminate\Support\Facades\Cache;

final class CountWaitlistSignupsAction
{
    public function handle(): int
    {
        $count = Cache::remember(
            'waitlistSignupsCount',
            now()->addWeek(),
            static fn (): int => WaitlistSignup::query()->count()
        );

        return intval($count);
    }
}
