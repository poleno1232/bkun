<?php

namespace App\Observers;

use App\DTO\ChatMessageTypeDto;
use App\DTO\KafkaMessageDto;
use App\Enums\ChatEventEnum;
use App\Models\ChatMessage;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class ChatMessageObserver
{
    public function created(ChatMessage $chatMessage)
    {
        $messageDto = ChatMessageTypeDto::make(
            $chatMessage->id, $chatMessage->message, $chatMessage->quest_id, $chatMessage->user_id
        );

        Kafka::publish()
            ->onTopic('chat-messages')
            ->withBody(
                KafkaMessageDto::make(ChatEventEnum::MESSAGE_CREATED, $messageDto)
            )->send();
    }

    public function updated(ChatMessage $chatMessage)
    {
        $messageDto = ChatMessageTypeDto::make(
            $chatMessage->id, $chatMessage->message, $chatMessage->quest_id, $chatMessage->user_id
        );

        Kafka::publish()
            ->onTopic('chat-messages')
            ->withBody(
                KafkaMessageDto::make(ChatEventEnum::MESSAGE_UPDATED, $messageDto)
            )->send();
    }
}
