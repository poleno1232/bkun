<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case USER = 'user';

    public static function labels(): array
    {
        return [
            self::ADMIN->value => 'Admin',
            self::MODERATOR->value => 'Moderator',
            self::USER->value => 'User',
        ];
    }
}
