<?php

namespace App\Http\Resources\Chats;

use App\Http\Resources\Users\UserResource;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ChatMessage $message */
        $message = $this->resource;

        return [
            'id' => $message->id,
            'message' => $message->message,
            'user' => $message->relationLoaded('user') ? UserResource::make($message->user) : null,
        ];
    }
}
