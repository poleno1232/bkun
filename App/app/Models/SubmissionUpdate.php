<?php

namespace App\Models;

use App\Contracts\Contentable;
use App\Enums\UpdateTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property ?string $name
 * @property string $title
 * @property string $content
 * @property Collection<Submission> $submissions
 * @property Quest $quest
 * @property int $quest_id
 * @property string $type
 */
#[Fillable([
    'quest_id',
    'chapter_id',
    'posted_at',
    'name',
])]
class SubmissionUpdate extends Model implements Contentable
{
    protected $with = [
        'submissions',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function content(): Attribute
    {
        return Attribute::get(fn () => $this->submissions->join('<br>'));
    }

    public function title(): Attribute
    {
        return Attribute::get(fn () => $this->name);
    }

    public function type(): Attribute
    {
        return Attribute::get(fn () => UpdateTypeEnum::SUBMISSION->value);
    }
}
