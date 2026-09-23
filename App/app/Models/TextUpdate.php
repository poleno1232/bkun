<?php

namespace App\Models;

use App\Contracts\Contentable;
use App\Enums\UpdateTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 *
 * @property ?string $title
 * @property string $text
 * @property Quest $quest
 * @property int $quest_id
 * @property string $type
 */
#[Fillable([
    'text',
    'chapter_id',
    'quest_id',
    'posted_at',
])]
class TextUpdate extends Model implements Contentable
{
    public function content(): Attribute
    {
        return Attribute::get(fn() => ['text' => $this->text]);
    }

    public function title(): Attribute
    {
        return Attribute::get(fn() => null);
    }

    public function type(): Attribute
    {
        return Attribute::get(fn () => UpdateTypeEnum::TEXT->value);
    }

    public function closes_at(): Attribute
    {
        return Attribute::get(fn() => null);
    }

    public function is_closed(): Attribute
    {
        return Attribute::get(fn() => null);
    }
}
