<?php

namespace App\Http\Resources\Chapters;

use App\Http\Resources\Update\UpdateResource;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Chapter $chapter */
        $chapter = $this->resource;
        return [
            'title' => $chapter->title,
            'updates' => $chapter->relationLoaded('submissionUpdates') && $chapter->relationLoaded('textUpdates')
                ? UpdateResource::collection($chapter->updates) : null,
        ];
    }
}
