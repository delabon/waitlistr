<?php

declare(strict_types=1);

use App\Http\Middleware\Admin;
use Database\Factories\UserFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('throws an HttpException when no signed-in user', function () {
    $adminMiddleware = new Admin();
    $request = new Request();
    $request->setUserResolver(static fn () => null);

    expect(fn () => $adminMiddleware->handle($request, static fn () => null))
        ->toThrow(HttpException::class);
});

it('throws an HttpException when the signed-in user is not an admin', function () {
    $adminMiddleware = new Admin();
    $user = UserFactory::new()->user()->create();
    $request = new Request();
    $request->setUserResolver(static fn () => $user);

    expect(fn () => $adminMiddleware->handle($request, static fn () => null))
        ->toThrow(HttpException::class);
});

it('passes the admin middleware successfully when the signed-in user is an admin', function () {
    $adminMiddleware = new Admin();
    $user = UserFactory::new()->admin()->create();
    $request = new Request();
    $request->setUserResolver(static fn () => $user);
    $responseContent = 'Admin is here!';

    $response = $adminMiddleware->handle($request, static fn () => new Response($responseContent));

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->getContent())->toBe($responseContent);
    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
});
