<?php

namespace App\Models;

use App\Contracts\Contentable;
use App\Enums\UpdateTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 *
 * @property ?string $title
 * @property string $text
 * @property Quest $quest
 * @property int $quest_id
 * @property string $type
 * @property null $closesAt
 */
#[Fillable([
    'text',
    'chapter_id',
    'quest_id',
    'posted_at',
])]
class TextUpdate extends Model implements Contentable
{

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

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

    public function closesAt(): Attribute
    {
        return Attribute::get(fn() => UpdateTypeEnum::TEXT->value);
    }

    public function isClosed(): Attribute
    {
        return Attribute::get(fn() => true);
    }
}
