<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\WaitlistSignups\GetWaitlistSignupsAction;
use App\Actions\WaitlistSignups\StoreWaitlistSignupAction;
use App\DTOs\WaitlistSignups\WaitlistSignupDTO;
use App\Http\Requests\StoreWaitlistSignupRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class WaitlistSignupController extends Controller
{
    private const int MAX_ITEMS_PER_PAGE = 10;

    public function index(GetWaitlistSignupsAction $action): Response
    {
        return Inertia::render('dashboard/admin/Signups', [
            'waitlistSignups' => $action(self::MAX_ITEMS_PER_PAGE),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Welcome');
    }

    public function store(
        StoreWaitlistSignupRequest $request,
        StoreWaitlistSignupAction $action
    ): RedirectResponse {
        $action(WaitlistSignupDTO::fromRequest($request));

        return back();
    }
}
