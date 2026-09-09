<?php

namespace App\DTO;

use App\Enums\ChatEventEnum;

class KafkaMessageDto implements \JsonSerializable
{
    protected function __construct(
        public readonly ChatEventEnum $messageType,
        public AbstractTypeDto $type
    )
    {
    }

    public static function make(ChatEventEnum $messageType, AbstractTypeDto $typeDto): KafkaMessageDto
    {
        return new KafkaMessageDto($messageType, $typeDto);
    }


    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->messageType->value,
            'message' => $this->type->jsonSerialize(),
        ];
    }
}
