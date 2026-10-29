<?php

namespace App\Policies;

use App\Enums\QuestStatusEnum;
use App\Enums\RoleEnum;
use App\Models\ChatMessage;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChatMessagePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(?User $user, ChatMessage $chatMessage): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->id === $chatMessage->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChatMessage $chatMessage): bool
    {
        return false;
    }
}
