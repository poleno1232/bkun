<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\UserResource;
use App\Models\Quest;
use App\Models\User;
use App\Services\QuestService;
use App\Services\UserService;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected QuestService $questService
    ) {}

    public function show(User $user)
    {
        return UserResource::make($user);
    }

    public function blockFromQuest(Quest $quest, User $user)
    {
        Gate::authorize('canBlockOrUnblock', $quest);
        $this->questService->blockUserInQuest($user, $quest);

        return response()->noContent();
    }

    public function unblockFromQuest(Quest $quest, User $user)
    {
        Gate::authorize('canBlockOrUnblock', $quest);
        $this->questService->unblockUserInQuest($user, $quest);

        return response()->noContent();
    }
}
