<?php

namespace App\Enums;

enum QuestStatusEnum : string
{
    case ACTIVE = 'active';
    case HIATUS = 'hiatus';
    case ABANDONED = 'abandoned';
    case UNLISTED = 'unlisted';
    case HIDDEN = 'hidden';

    public static function labels(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::HIATUS->value => 'Hiatus',
            self::ABANDONED->value => 'Abandoned',
            self::UNLISTED->value => 'Unlisted',
            self::HIDDEN->value => 'Hidden',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public static function allowed(): array
    {
        return [
            self::ACTIVE->value,
            self::HIATUS->value,
            self::ABANDONED->value,
            self::HIDDEN->value,
        ];
    }

    public static function viewable(QuestStatusEnum $questStatus): bool
    {
        return in_array($questStatus, [
            self::ACTIVE,
            self::HIATUS,
            self::ABANDONED,
        ]);
    }
}
