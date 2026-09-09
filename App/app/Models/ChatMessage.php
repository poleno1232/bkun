<?php

namespace App\Models;

use App\Observers\ChatMessageObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin Builder
 *
 * @property int $id
 * @property int $user_id
 * @property int $quest_id
 * @property string $message
 *
 * @property Quest $quest
 * @property User $user
 */
#[Fillable(
    'quest_id',
    'user_id',
    'message',
    'posted_at',
    'ip',
)]
#[ObservedBy(ChatMessageObserver::class)]
class ChatMessage extends Model
{
    use SoftDeletes;
    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
