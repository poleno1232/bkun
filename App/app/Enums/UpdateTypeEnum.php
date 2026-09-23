<?php

namespace App\Enums;

enum UpdateTypeEnum: string
{
    case TEXT = 'text';
    case SUBMISSION = 'submission';
    case VOTE = 'vote';

    public static function values(): array
    {
        return [
            self::TEXT->value,
            self::SUBMISSION->value,
            self::VOTE->value,
        ];
    }
}
