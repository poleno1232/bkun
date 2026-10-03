<?php

namespace App\Policies;

use App\Enums\QuestStatusEnum;
use App\Models\Quest;
use App\Models\User;

class QuestPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Quest $quest): bool
    {
        if (optional($user)?->id === $quest->user_id) {
            return true;
        }

        return QuestStatusEnum::viewable($quest->status);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quest $quest): bool
    {
        return $quest->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quest $quest): bool
    {
        return $quest->user_id == $user->id;
    }
}
