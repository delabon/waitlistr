<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreWaitlistSignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'firstName' => [
                'nullable',
                'string',
                'min:2',
                'max:255',
            ],
            'lastName' => [
                'nullable',
                'string',
                'min:2',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'unique:waitlist_signups,email',
            ],
        ];
    }
}
