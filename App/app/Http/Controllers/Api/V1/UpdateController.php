<?php

namespace App\Http\Controllers\Api\V1;

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
}
