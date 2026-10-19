<?php

namespace Tests\Feature;

use App\Enums\QuestStatusEnum;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Support\Uri;
use Junges\Kafka\Facades\Kafka;
use Laravel\Sanctum\PersonalAccessToken;

class QuestTest extends TestCase
{
    protected const INDEX_RESPONSE = [
        'id',
        'name',
        'description',
        'image_url',
        'status',
    ];

    protected const array CREATE_REQUEST = [
        'name' => 'Test Quest',
        'description' => 'Test Quest',
        'image_url' => null,
    ];

    protected const array UPDATE_REQUEST = [
        'name' => 'Test Quest Edited',
        'description' => 'Test Quest Desc',
        'image_url' => null,
        'status' => QuestStatusEnum::ACTIVE->value,
        'verified_only' => true,
    ];
    /**
     * A basic feature test example.
     */
    public function testIndexSuccessful(): void
    {
        $response = $this->request(
            Uri::route('quests.index'),
            'get',
            params: [
                'status' => QuestStatusEnum::ACTIVE->value,
                'name' => 'Test Quest',
            ],
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['*' => array_merge(self::INDEX_RESPONSE, self::USER_SUBRESPONSE)]
        ]);

        $response = $this->request(
            Uri::route('quests.index'),
            'get',
            params: [
                'name' => 'Test Quest',
            ],
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['*' => array_merge(self::INDEX_RESPONSE, self::USER_SUBRESPONSE)]
        ]);

        $response = $this->request(
            Uri::route('quests.index'),
            'get',
            params: [
                'user_id' => User::firstOrFail()->id,
            ],
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['*' => array_merge(self::INDEX_RESPONSE, self::USER_SUBRESPONSE)]
        ]);
    }

    public function testShowSuccessful(): void
    {
        $response = $this->request(Uri::route('quests.show', Quest::first()->id), 'get');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => array_merge(self::INDEX_RESPONSE, self::USER_SUBRESPONSE, self::CHAPTER_SUBRESPONSE)
        ]);
        $this->clearToken();

        $this->authenticateUser();
        $this->assertNotEmpty(PersonalAccessToken::all());
        $response = $this->request(Uri::route('quests.show', Quest::first()->id), 'get');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => array_merge(self::INDEX_RESPONSE, self::USER_SUBRESPONSE, self::CHAPTER_SUBRESPONSE)
        ]);
    }

    public function testShowNotFoundBecauseNotTheUser(): void
    {
        $this->authenticateAnotherUser();
        $this->assertNotEmpty(PersonalAccessToken::all());
        $quest = Quest::first();
        $quest->update(['status' => QuestStatusEnum::UNLISTED]);
        $response = $this->request(Uri::route('quests.show', Quest::first()->id), 'get');

        $response->assertStatus(403);
    }

    public function testCreateQuestSuccessful(): void
    {
        $this->authenticateUser();
        $this->assertNotEmpty(PersonalAccessToken::all());

        $response = $this->request(Uri::route('quests.store'), 'post', data: self::CREATE_REQUEST);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => array_merge(self::INDEX_RESPONSE, self::USER_SUBRESPONSE, self::CHAPTER_SUBRESPONSE)
        ]);

        Kafka::assertPublishedOn('chat-messages');
    }

    public function testCreateQuestUnprocessable(): void
    {
        $this->authenticateUser();
        $this->assertNotEmpty(PersonalAccessToken::all());

        $response = $this->request(Uri::route('quests.store'), 'post');
        $response->assertStatus(422);
        $response->assertJsonStructure(self::UNPROCESSABLE_ENTITY_RESPONSE);
    }

    public function testCreateQuestUnauthorized(): void
    {
        $response = $this->request(Uri::route('quests.store'), 'post');
        $response->assertStatus(401);
    }

    public function testUpdateQuestSuccessful(): void
    {
        $this->authenticateUser();
        $this->assertNotEmpty(PersonalAccessToken::all());

        $response = $this->request(
            Uri::route('quests.update', Quest::first()->id),
            'put',
            data: self::UPDATE_REQUEST
        );
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => array_merge(self::INDEX_RESPONSE, self::USER_SUBRESPONSE, self::CHAPTER_SUBRESPONSE)
        ]);
    }

    public function testUpdateQuestUnprocessable(): void
    {
        $this->authenticateUser();
        $this->assertNotEmpty(PersonalAccessToken::all());

        $response = $this->request(Uri::route('quests.update', Quest::first()->id), 'put');
        $response->assertStatus(422);
        $response->assertJsonStructure(self::UNPROCESSABLE_ENTITY_RESPONSE);
    }

    public function testUpdateQuestUnauthorized(): void
    {
        $response = $this->request(Uri::route('quests.update', Quest::first()->id), 'put');
        $response->assertStatus(401);
    }
}
