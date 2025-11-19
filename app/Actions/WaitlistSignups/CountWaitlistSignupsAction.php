<?php

declare(strict_types=1);

namespace App\Actions\WaitlistSignups;

use App\Models\WaitlistSignup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

final class CountWaitlistSignupsAction
{
    public function __invoke(): string
    {
        return Cache::remember(
            'waitlistSignupsCount',
            now()->addWeek(),
            static fn () => Number::forHumans(
                number: WaitlistSignup::query()->count(),
                abbreviate: true
            )
        );
    }
}
