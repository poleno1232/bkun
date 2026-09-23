<?php

namespace App\Models;

use App\Contracts\Contentable;
use App\Observers\ChapterObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 *
 * @property string $title
 * @property int $quest_id
 * @property int $order
 * @property Collection<Contentable> $updates
 *
 * @property Quest $quest
 * @property Collection<TextUpdate>|array $textUpdates
 * @property Collection<SubmissionUpdate>|array $submissionUpdates
 * @property Collection<VoteUpdate>|array $voteUpdates
 */
#[Fillable([
    'title',
    'quest_id',
    'order',
])]
#[ObservedBy(ChapterObserver::class)]
class Chapter extends Model
{
    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }

    public function textUpdates(): HasMany
    {
        return $this->hasMany(TextUpdate::class);
    }

    public function submissionUpdates(): HasMany
    {
        return $this->hasMany(SubmissionUpdate::class);
    }

    public function votesUpdates(): HasMany
    {
        return $this->hasMany(VoteUpdate::class);
    }

    public function withUpdates(): Chapter
    {
        return $this->load(['textUpdates', 'submissionUpdates', 'votesUpdates']);
    }

    protected function updates(): Attribute
    {
        return Attribute::get(function () {
            return $this->withUpdates()
                ->textUpdates
                ->concat($this->submissionUpdates)
                ->concat($this->voteUpdates)
                ->sortBy('posted_at')->values();
        });
    }
}
