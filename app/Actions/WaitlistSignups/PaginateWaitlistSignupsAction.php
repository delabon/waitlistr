<?php

declare(strict_types=1);

namespace App\Actions\WaitlistSignups;

use App\Models\WaitlistSignup;
use Illuminate\Pagination\LengthAwarePaginator;

final class PaginateWaitlistSignupsAction
{
    /**
     * @return LengthAwarePaginator<int, WaitlistSignup>
     */
    public function __invoke(int $maxItemsPerPage = 10): LengthAwarePaginator
    {
        return WaitlistSignup::query()
            ->latest('id')
            ->paginate($maxItemsPerPage);
    }
}
