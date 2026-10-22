<?php

namespace App\Policies;

use App\Enums\QuestStatusEnum;
use App\Enums\RoleEnum;
use App\Models\BlockedUserQuest;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Support\Facades\Request;

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

    public function canPostInQuest(?User $user, Quest $quest): bool
    {
        if (!$user && $quest->verified_only) {
            return false;
        }

        if ($user?->id == $quest->user_id || $user?->hasAnyRole([RoleEnum::ADMIN, RoleEnum::MODERATOR])) {
            return true;
        }

        $isBlocked = BlockedUserQuest::where(function ($query) use ($user, $quest) {
            $ip = Request::ip();
            return $query->orWhere('user_id', $user?->id)->orWhere('ip', $ip);
        })->where('quest_id', $quest->id)->exists();

        if ($isBlocked) {
            return false;
        }

        return true;
    }

    public function canBlockOrUnblock(User $user, Quest $quest): bool
    {
        return $quest->user_id == $user->id || $user->hasAnyRole([RoleEnum::ADMIN]);
    }
}
