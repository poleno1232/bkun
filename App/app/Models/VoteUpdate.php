<?php

namespace App\Models;

use App\Contracts\Contentable;
use App\Enums\UpdateTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 * @property int $id
 * @property int $chapter_id
 * @property string $name
 *
 * @property Collection<Vote> $votes
 *
 * @property null|\DateTime $closed_at
 * @property null|bool $is_closed
 */
#[Fillable([
    'name',
    'posted_at',
    'chapter_id',
    'closes_at',
    'is_closed',
])]
class VoteUpdate extends Model implements Contentable
{
    protected $with = ['votes'];



    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function content(): Attribute
    {
        return Attribute::get(function () {
            return $this->votes->map(fn (Vote $vote) => [
                'id' => $vote->id,
                'value' => $vote->value,
                'voters_total' => $vote->voters_count,
                'anons_count' => $vote->anons_count,
                'users_count' => $vote->users_count,
            ])->sortByDesc('voters_total');
        });
    }

    public function title(): Attribute
    {
        return Attribute::get(fn () => $this->name);
    }

    public function type(): Attribute
    {
        return Attribute::get(fn () => UpdateTypeEnum::VOTE->value);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function closesAt(): Attribute
    {
        return Attribute::get(fn () => $this->closed_at);
    }

    public function isClosed(): Attribute
    {
        return Attribute::get(fn () => (bool)($this->attributes['is_closed'] ?? null));
    }
}
