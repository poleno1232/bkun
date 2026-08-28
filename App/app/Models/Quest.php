<?php

namespace App\Models;

use App\Observers\QuestObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
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
 * @property bool $is_active
 * @property \DateTime $next_live_at
 * @property User $user
 * @property Collection<Chapter> $chapters
 */
#[Fillable([
    'name',
    'description',
    'image_url',
    'user_id',
    'is_active',
    'next_live_at',
])]
#[ObservedBy(QuestObserver::class)]
class Quest extends Model
{
    protected $casts = [
        'is_active' => 'boolean',
        'next_live_at' => 'datetime',
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
