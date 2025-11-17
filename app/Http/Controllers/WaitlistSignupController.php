<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\WaitlistSignups\StoreWaitlistSignupAction;
use App\DTOs\WaitlistSignups\WaitlistSignupDTO;
use App\Http\Requests\StoreWaitlistSignupRequest;
use App\Http\Resources\WaitlistSignupResource;
use App\Models\WaitlistSignup;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class WaitlistSignupController extends Controller
{
    private const int MAX_ITEMS_PER_PAGE = 10;

    public function index(): Response
    {
        $waitlistSignups = WaitlistSignup::query()->latest('id')->paginate(self::MAX_ITEMS_PER_PAGE);

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
        $action(WaitlistSignupDTO::fromRequest($request));

        return back();
    }
}
