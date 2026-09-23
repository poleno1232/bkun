<?php

namespace App\Observers;

use App\DTO\ChatMessageTypeDto;
use App\DTO\KafkaMessageDto;
use App\Enums\ChatEventEnum;
use App\Models\Chapter;
use Junges\Kafka\Facades\Kafka;

class ChapterObserver
{
    public function creating(Chapter $chapter)
    {
        // Ensure correct order
        $chapter->order = (Chapter::where('quest_id', $chapter->quest_id)->max('order') ?? -1) + 1;
    }

    public function created(Chapter $chapter)
    {
        $msg = "New chapter published! " . $chapter->title;
        $messageDto = ChatMessageTypeDto::make(
            $chapter->id, $msg, $chapter->quest_id, $chapter->quest->user_id
        );

        Kafka::publish()
            ->onTopic('chat-messages')
            ->withKafkaKey($chapter->quest_id)
            ->withBody(
                KafkaMessageDto::make(ChatEventEnum::NEW_CHAPTER, $messageDto)
            )->send();
    }
}
