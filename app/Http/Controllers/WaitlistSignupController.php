<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\WaitlistSignups\PaginateWaitlistSignupsAction;
use App\Actions\WaitlistSignups\StoreWaitlistSignupAction;
use App\Http\Requests\StoreWaitlistSignupRequest;
use App\Http\Resources\WaitlistSignupResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class WaitlistSignupController extends Controller
{
    public const int MAX_ITEMS_PER_PAGE = 10;

    public function index(PaginateWaitlistSignupsAction $action): Response
    {
        $waitlistSignups = $action(self::MAX_ITEMS_PER_PAGE);

        return Inertia::render('dashboard/admin/Signups', [
            'waitlistSignups' => WaitlistSignupResource::collection($waitlistSignups),
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
        $action($request->toDto());

        return back();
    }
}
