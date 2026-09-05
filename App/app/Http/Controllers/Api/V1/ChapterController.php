<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chapters\ChapterRequest;
use App\Http\Resources\Chapters\ChapterResource;
use App\Models\Chapter;
use App\Models\Quest;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function show(int $quest, Chapter $chapter)
    {
        $chapter->withUpdates();

        return ChapterResource::make($chapter);
    }

    public function store(ChapterRequest $request, Quest $quest)
    {
        $chapter = Chapter::create(array_merge($request->validated(), ['quest_id' => $quest->id]));

        return ChapterResource::make($chapter);
    }

    public function update(ChapterRequest $request, Quest $quest, Chapter $chapter)
    {
        $chapter->update(array_merge($request->validated(), ['quest_id' => $quest->id]));
        $chapter->withUpdates();

        return ChapterResource::make($chapter);
    }
}
