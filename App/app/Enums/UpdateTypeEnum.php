<?php

namespace App\Enums;

enum UpdateTypeEnum: string
{
    case TEXT = 'text';
    case SUBMISSION = 'submission';

    public static function values(): array
    {
        return [
            self::TEXT->value,
            self::SUBMISSION->value,
        ];
    }
}
