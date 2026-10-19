<?php

namespace Tests\Unit;

use App\DTO\ChatMessageTypeDto;
use PHPUnit\Framework\TestCase;

class DTOTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function testChatMessageDTO(): void
    {
        $this->assertFalse($this->hasError([1, 'test', 1, null]));
        $this->assertTrue($this->hasError([1]));
    }

    protected function hasError(array $args): bool
    {
        $hasError = false;

        try {
            ChatMessageTypeDto::make(...$args);
        } catch (\RuntimeException) {
            $hasError = true;
        }

        return $hasError;
    }
}
