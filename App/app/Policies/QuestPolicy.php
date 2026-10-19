<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\QuestStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Quest;
use App\Models\User;

class QuestPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Quest $quest): bool
    {
        $optional = optional($user);
        if (
            $optional?->id === $quest->user_id
            || $user?->hasAnyRole([RoleEnum::ADMIN, RoleEnum::MODERATOR])
        ) {
            return true;
        }

        return QuestStatusEnum::viewable($quest->status);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quest $quest): bool
    {
        return $quest->user_id === $user->id || $user->hasAnyRole([RoleEnum::ADMIN, RoleEnum::MODERATOR]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quest $quest): bool
    {
        return $quest->user_id == $user->id || $user->hasAnyRole([RoleEnum::ADMIN, RoleEnum::MODERATOR]);
    }

    public function canHaveAnonymousCommenters(?User $user, Quest $quest): bool
    {
        if (!$quest->verified_only) {
            return true;
        }

        return $quest->verified_only && $user;
    }
}
