<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UpdateTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Updates\UpdateRequest;
use App\Http\Resources\Update\UpdateResource;
use App\Models\Chapter;
use App\Services\UpdateService;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __construct(protected UpdateService $updateService)
    {
    }

    public function show(int $chapter, int $updateId, UpdateTypeEnum $type): UpdateResource
    {
        $update = $this->updateService->getUpdate($updateId, $type);

        return UpdateResource::make($update);
    }

    public function store(UpdateRequest $request, Chapter $chapter)
    {
        $validated = $request->validated();
        $update = $this->updateService->create($validated, $chapter);

        return UpdateResource::make($update);
    }

    public function update(UpdateRequest $request, Chapter $chapter, int $updateId)
    {
        $validated = $request->validated();
        $update = $this->updateService->edit($validated, $updateId);

        return UpdateResource::make($update);
    }

    public function open(int $updateId)
    {
        $update = $this->updateService->open($updateId);
        return UpdateResource::make($update);
    }

    public function close(int $chapterId, int $updateId)
    {
        $update = $this->updateService->close($updateId);
        return UpdateResource::make($update);
    }
}
