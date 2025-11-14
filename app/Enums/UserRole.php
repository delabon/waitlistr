<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Collection;

enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';

    /**
     * @return array<mixed>
     */
    public static function toArray(): array
    {
        // In your conventions you said no to use helper methods in PHP code
        return new Collection(self::cases())
            ->mapWithKeys(static fn (self $case) => [
                $case->value => $case->name,
            ])
            ->toArray();
    }
}
