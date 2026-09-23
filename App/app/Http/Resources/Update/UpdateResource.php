<?php

namespace App\Http\Resources\Update;

use App\Contracts\Contentable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Contentable $update */
        $update = $this->resource;

        return [
            'id' => $update->id,
            'type' => $update->type,
            'title' => $update->title,
            'is_closed' => $update->is_closed,
            'closes_at' => $update->closes_at,
            'content' => $update->content
        ];
    }
}
