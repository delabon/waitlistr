<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class WaitlistSignup extends Model
{
    /** @use HasFactory<\Database\Factories\WaitlistSignupFactory> */
    use HasFactory;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'welcome_email_sent_at',
    ];

    protected $casts = [
        'welcome_email_sent_at' => 'datetime',
    ];
}
