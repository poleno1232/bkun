<?php

namespace App\Models;

use App\Contracts\Contentable;
use App\Enums\UpdateTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 *
 * @property int $id
 * @property ?string $name
 * @property string $title
 * @property string $content
 * @property Collection<Submission> $submissions
 * @property Quest $quest
 * @property int $quest_id
 * @property string $type
 * @property null|\DateTime $closes_at
 * @property null|bool $is_closed
 */
#[Fillable([
    'quest_id',
    'chapter_id',
    'posted_at',
    'name',
    'closed_at',
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
        return Attribute::get(
            function () {
                return $this->submissions->map(fn (Submission $submission) => [
                    'id' => $submission->id,
                    'submission_update_id' => $submission->submission_update_id,
                    'user_id' => $submission->user_id,
                    'text' => $submission->text,
                ]);
            }
        );
    }

    public function title(): Attribute
    {
        return Attribute::get(fn () => $this->name);
    }

    public function type(): Attribute
    {
        return Attribute::get(fn () => UpdateTypeEnum::SUBMISSION->value);
    }

    public function closes_at(): Attribute
    {
        return Attribute::get(fn () => $this->closes_at);
    }

    public function is_closed(): Attribute
    {
        return Attribute::get(fn () => $this->is_closed);
    }
}
