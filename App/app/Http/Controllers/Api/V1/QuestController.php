<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quests\QuestRequest;
use App\Http\Requests\Quests\UpdateQuestRequest;
use App\Http\Requests\SearchQuestRequest;
use App\Http\Resources\Quests\IndexQuestResource;
use App\Http\Resources\Quests\QuestResource;
use App\Models\Quest;
use App\Services\QuestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuestController extends Controller
{
    public function __construct(
        private readonly QuestService $questService
    ){

    }

    public function index(SearchQuestRequest $request)
    {
        $this->ensureUserIsAuthenticated();
        return IndexQuestResource::collection(
            $this->questService
                ->getQuery($request->validated())
                ->paginate()
        );
    }

    public function show(Quest $quest): QuestResource
    {
        $this->ensureUserIsAuthenticated();

        Gate::authorize('view', $quest);
        $quest->load(['user', 'chapters']);

        return QuestResource::make($this->questService->show($quest));
    }

    public function store(QuestRequest $request): QuestResource
    {
        $quest = $this->questService->create($request->validated());

        return QuestResource::make($quest);
    }

    public function update(Quest $quest, UpdateQuestRequest $request): QuestResource
    {
        Gate::authorize('update', $quest);
        $quest = $this->questService->update($quest, $request->validated());

        return QuestResource::make($quest);
    }
}
