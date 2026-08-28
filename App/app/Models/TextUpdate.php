<?php

namespace App\Models;

use App\Contracts\Contentable;
use App\Traits\QuestUpdate;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 *
 * @property string $text
 * @property Quest $quest
 * @property int $quest_id
 */
#[Fillable([
    'text',
    'chapter_id',
    'quest_id',
])]
class TextUpdate extends Model implements Contentable
{
    use QuestUpdate;

    public function content(): Attribute
    {
        return Attribute::get(fn() => $this->text);
    }
}
