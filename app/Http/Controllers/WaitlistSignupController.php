<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\WaitlistSignups\StoreWaitlistSignupAction;
use App\DTOs\WaitlistSignups\WaitlistSignupDTO;
use App\Http\Requests\StoreWaitlistSignupRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class WaitlistSignupController extends Controller
{
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
