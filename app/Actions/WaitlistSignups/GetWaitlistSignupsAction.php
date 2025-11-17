<?php

declare(strict_types=1);

namespace App\Actions\WaitlistSignups;

use App\Models\WaitlistSignup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class GetWaitlistSignupsAction
{
    public function __invoke(int $maxItemsPerPage = 10): LengthAwarePaginator
    {
        return WaitlistSignup::query()->latest('id')->paginate($maxItemsPerPage);
    }
}
