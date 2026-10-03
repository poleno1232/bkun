<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UpdateTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Updates\UpdateRequest;
use App\Http\Resources\Update\UpdateResource;
use App\Models\Chapter;
use App\Services\UpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UpdateController extends Controller
{
    public function __construct(protected UpdateService $updateService)
    {
    }

    public function show(Chapter $chapter, int $updateId, UpdateTypeEnum $type): UpdateResource
    {
        Gate::authorize('view', $chapter->quest);
        $update = $this->updateService->getUpdate($updateId, $type);

        return UpdateResource::make($update);
    }

    public function store(UpdateRequest $request, Chapter $chapter)
    {
        Gate::authorize('update', $chapter->quest);
        $validated = $request->validated();
        $update = $this->updateService->create($validated, $chapter);

        return UpdateResource::make($update);
    }

    public function update(UpdateRequest $request, Chapter $chapter, int $updateId)
    {
        Gate::authorize('update', $chapter->quest);
        $validated = $request->validated();
        $update = $this->updateService->edit($validated, $updateId);

        return UpdateResource::make($update);
    }

    public function open(Chapter $chapter, int $updateId)
    {
        Gate::authorize('update', $chapter->quest);
        $update = $this->updateService->open($updateId);
        return UpdateResource::make($update);
    }

    public function close(Chapter $chapter, int $updateId)
    {
        Gate::authorize('update', $chapter->quest);
        $update = $this->updateService->close($updateId);
        return UpdateResource::make($update);
    }
}
