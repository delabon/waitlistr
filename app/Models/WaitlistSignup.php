<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WaitlistSignup extends Model
{
    private const string DATETIME_FORMAT = 'M j, Y - h:i A';

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'welcome_email_sent_at',
    ];

    protected $casts = [
        'welcome_email_sent_at' => 'datetime',
    ];

    public function getFormattedCreatedAtAttribute(): string
    {
        if (!$this->created_at) {
            return '';
        }

        return $this->created_at->format(self::DATETIME_FORMAT);
    }

    public function getFormattedWelcomeEmailSentAtAttribute(): string
    {
        if (!$this->welcome_email_sent_at) {
            return '';
        }

        return $this->welcome_email_sent_at->format(self::DATETIME_FORMAT);
    }
}
