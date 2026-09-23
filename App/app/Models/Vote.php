<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


/**
 * @property int $id
 * @property string $value
 *
 * @property Collection<User> $voters
 * @property Collection<User> $anons
 * @property Collection<User> $users
 *
 * @property int $voters_count
 * @property int $anons_count
 * @property int $users_count
 */
#[Fillable([
    'value',
    'vote_update_id',
])]
class Vote extends Model
{
    protected $withCount = ['anons', 'voters', 'users'];

    public function voters(): HasMany
    {
        return $this->hasMany(VoteUser::class)->with('user');
    }

    public function anons(): HasMany
    {
        return $this->hasMany(VoteUser::class)->whereNull('user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(VoteUser::class)->whereNotNull('user_id')->with('user');
    }
}
