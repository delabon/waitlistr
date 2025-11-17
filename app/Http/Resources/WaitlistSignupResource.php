<?php

namespace App\Http\Resources;

use App\Models\WaitlistSignup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property WaitlistSignup $resource
 */
final class WaitlistSignupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'email' => $this->resource->email,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'formatted_created_at' => $this->resource->formatted_created_at,
            'formatted_welcome_email_sent_at' => $this->resource->formatted_welcome_email_sent_at,
        ];
    }
}
