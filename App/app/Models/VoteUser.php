<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Table(incrementing: true)]
class VoteUser extends Pivot
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vote(): BelongsTo
    {
        return $this->belongsTo(Vote::class);
    }
}
