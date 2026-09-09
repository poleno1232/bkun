<?php

namespace App\DTO;

class ChatMessageTypeDto extends AbstractTypeDto
{
    protected function __construct(
        public readonly int $id,
        public readonly string $message,
        public readonly int $questId,
        public readonly ?int $userId,
    )
    {

    }

    /**
     * @param int $id
     * @param string $message
     * @param int $questId
     * @param ?int $userId
     * @return ChatMessageTypeDto
     */
    public static function make(...$arguments): ChatMessageTypeDto
    {
        if (func_num_args() != 4) {
            throw new \RuntimeException("Incorrect amount of arguments supplied");
        }

        return new ChatMessageTypeDto(...$arguments);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'quest_id' => $this->questId,
            'user_id' => $this->userId,
        ];
    }
}
