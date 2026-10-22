<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 *
 * @property int $quest_id
 * @property ?int $user_id
 * @property ?string $ip_address
 *
 * @property-read ?User $user
 * @property-read Quest $quest
 */
#[Fillable([
    'user_id',
    'quest_id',
    'ip_address',
])]
class BlockedUserQuest extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }
}
