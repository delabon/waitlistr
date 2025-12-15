<?php

declare(strict_types=1);

use App\DTOs\WaitlistSignups\WaitlistSignupDTO;
use Database\Factories\WaitlistSignupFactory;
use Illuminate\Contracts\Support\Arrayable;

it('creates an instance of Arrayable', function () {
    $dto = new WaitlistSignupDTO(
        firstName: 'John',
        lastName: 'Doe',
        email: 'john@doe.test',
    );

    expect($dto)->toBeInstanceOf(Arrayable::class);
});

it('creates an object from an array with camel case keys', function () {
    $data = [
        'firstName' => 'Mike',
        'lastName' => 'Dope',
        'email' => 'mike@dope.test',
    ];

    $dto = WaitlistSignupDTO::fromArray($data);

    expect($dto->firstName)->toBe($data['firstName'])
        ->and($dto->lastName)->toBe($data['lastName'])
        ->and($dto->email)->toBe($data['email']);
});

it('creates an object from an array with snake case keys', function () {
    $data = [
        'first_name' => 'Mike',
        'last_name' => 'Dope',
        'email' => 'mike@dope.test',
    ];

    $dto = WaitlistSignupDTO::fromArray($data);

    expect($dto->firstName)->toBe($data['first_name'])
        ->and($dto->lastName)->toBe($data['last_name'])
        ->and($dto->email)->toBe($data['email']);
});

it('creates an object from a WaitlistSignup model', function () {
    $model = WaitlistSignupFactory::new()->create();

    $dto = WaitlistSignupDTO::fromModel($model);

    expect($dto->firstName)->toBe($model->first_name)
        ->and($dto->lastName)->toBe($model->last_name)
        ->and($dto->email)->toBe($model->email);
});
