<?php

namespace App\Services;

use App\Http\Requests\Chats\UpdateChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\Quest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChatService
{
    public function create(array $data, Quest $quest): Model|ChatMessage
    {
        $data['quest_id'] = $quest->id;

        return ChatMessage::create($data);
    }

    public function update(array $data, ChatMessage $message, string $ip): bool
    {
        if ($message->ip != $ip) {
            throw ValidationException::withMessages([
                'ip' => "Message does not belong to IP address",
            ]);
        }

        return $message->update($data);
    }
}
