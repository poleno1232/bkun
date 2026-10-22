<?php

namespace Tests\Feature;

use App\Enums\QuestStatusEnum;
use App\Models\BlockedUserQuest;
use App\Models\ChatMessage;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Support\Uri;

class ChatMessageTest extends TestCase
{
    protected const array CHAT_MESSAGE_RESPONSE = [
        'id',
        'message',
        'user',
    ];
    /**
     * A basic feature test example.
     */
    public function testIndexMessagesSuccess(): void
    {
        $quest = Quest::firstOrFail();
        $response = $this->request(Uri::route('chats.index', ['quest' => $quest->id]), 'GET');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['*' => self::CHAT_MESSAGE_RESPONSE]]);
    }

    public function testIndexMessagesUnlistedQuest(): void
    {
        $quest = Quest::firstOrFail();
        $quest->update(['status' => QuestStatusEnum::UNLISTED->value]);
        $response = $this->request(Uri::route('chats.index', ['quest' => $quest->id]), 'GET');

        $response->assertStatus(403);
    }

    public function testShowMessageSuccess(): void
    {
        $msg = ChatMessage::with('quest')->firstOrFail();
        $response = $this->request(
            Uri::route('chats.show', ['quest' => $msg->quest->id, 'message' => $msg->id]),
            'GET',
        );

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => self::CHAT_MESSAGE_RESPONSE]);
    }

    public function testCreateMessageSuccess(): void
    {
        $quest = Quest::firstOrFail();
        $quest->update(['verified_only' => false]);
        $response = $this->request(
            Uri::route('chats.store', ['quest' => $quest->id]),
            'POST',
            data: [
                'message' => 'Test message',
            ],
        );

        $response->assertStatus(201);
    }

    public function testCreateMessageVerifiedOnly()
    {
        $quest = Quest::firstOrFail();
        $quest->update(['verified_only' => true]);
        $response = $this->request(
            Uri::route('chats.store', ['quest' => $quest->id]),
            'POST',
            data: [
                'message' => 'Test message',
            ],
        );

        $response->assertStatus(403);

        $this->authenticateUser();
        $response = $this->request(
            Uri::route('chats.store', ['quest' => $quest->id]),
            'POST',
            data: [
                'message' => 'Test message',
            ],
        );

        $response->assertStatus(201);
    }

    public function testCreateMessageFailBanned()
    {
        $quest = Quest::where(
            'user_id',
            User::where('name', self::USER_DATA['name'])->firstOrFail()->id
        )->firstOrFail();
        BlockedUserQuest::create([
            'user_id' => User::where('name', self::ANOTHER_USER_DATA['name'])->firstOrFail()->id,
            'quest_id' => $quest->id,
            'ip_address' => '127.0.0.1',
        ]);

        $this->authenticateAnotherUser();
        $response = $this->request(
            Uri::route('chats.store', ['quest' => $quest->id]),
            'POST',
            data: [
                'message' => 'Test message',
            ],
        );

        $response->assertStatus(403);
    }

    public function testUpdateMessageSuccess()
    {
        $quest = Quest::firstOrFail();
        $this->authenticateUser();
        $msg = ChatMessage::with(['quest', 'user'])->firstOrFail();

        $response = $this->request(
            Uri::route('chats.update', ['quest' => $quest->id, 'message' => $msg->id]),
            'PUT',
            data: [
                'message' => 'Test message',
            ]
        );

        $response->assertStatus(204);
    }

    public function testUpdateMessageFailedAnonymous()
    {
        $quest = Quest::firstOrFail();
        $msg = ChatMessage::with(['quest', 'user'])->firstOrFail();

        $response = $this->request(
            Uri::route('chats.update', ['quest' => $quest->id, 'message' => $msg->id]),
            'PUT',
            data: [
                'message' => 'Test message',
            ]
        );

        $response->assertStatus(403);

        $quest = Quest::firstOrFail();
        $this->authenticateAnotherUser();
        $msg = ChatMessage::with(['quest', 'user'])->firstOrFail();

        $response = $this->request(
            Uri::route('chats.update', ['quest' => $quest->id, 'message' => $msg->id]),
            'PUT',
            data: [
                'message' => 'Test message',
            ]
        );

        $response->assertStatus(403);
    }

    public function testUpdateMessageUnprocessable()
    {
        $quest = Quest::firstOrFail();
        $this->authenticateUser();
        $msg = ChatMessage::with(['quest', 'user'])->firstOrFail();
        $msg->update(['ip' => '127.0.0.2']);

        $response = $this->request(
            Uri::route('chats.update', ['quest' => $quest->id, 'message' => $msg->id]),
            'PUT',
            data: [
                'message' => 'Test message',
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(self::UNPROCESSABLE_ENTITY_RESPONSE);
    }
}
