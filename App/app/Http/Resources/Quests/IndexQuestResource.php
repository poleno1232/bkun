<?php

namespace App\Http\Resources\Quests;

use App\Http\Resources\Users\UserResource;
use App\Models\Quest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexQuestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Quest $quest */
        $quest = $this->resource;

        return [
            'id' => $quest->id,
            'name' => $quest->name,
            'description' => $quest->description,
            'image_url' => $quest->image_url,
            'status' => $quest->status->label(),
            'user' => $quest->relationLoaded('user') ? UserResource::make($quest->user) : null,
        ];
    }
}
