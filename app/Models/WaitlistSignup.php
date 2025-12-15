<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read string $email
 * @property-read string $first_name
 * @property-read string $last_name
 * @property-read CarbonInterface|null $welcome_email_sent_at
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class WaitlistSignup extends Model
{
    public const string DATETIME_FORMAT = 'M j, Y - h:i A';

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
        return $this->created_at?->format(self::DATETIME_FORMAT) ?? '';
    }

    public function getFormattedWelcomeEmailSentAtAttribute(): string
    {
        return $this->welcome_email_sent_at?->format(self::DATETIME_FORMAT) ?? '';
    }
}
