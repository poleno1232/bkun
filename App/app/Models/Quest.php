<?php

namespace App\Models;

use App\Enums\QuestStatusEnum;
use App\Observers\QuestObserver;
use App\Policies\QuestPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Builder
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property ?string $image_url
 * @property QuestStatusEnum $status
 * @property \DateTime $next_live_at
 * @property bool $verified_only
 * @property int $user_id
 * @property User $user
 * @property Collection<Chapter> $chapters
 */
#[Fillable([
    'name',
    'description',
    'image_url',
    'user_id',
    'status',
    'next_live_at',
    'verified_only',
])]
#[ObservedBy(QuestObserver::class)]
#[UsePolicy(QuestPolicy::class)]
class Quest extends Model
{
    protected $casts = [
        'next_live_at' => 'datetime',
        'status' => QuestStatusEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }
}
