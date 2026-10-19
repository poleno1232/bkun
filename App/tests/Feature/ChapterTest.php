<?php

namespace Tests\Feature;

use App\Enums\QuestStatusEnum;
use App\Models\Chapter;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Support\Uri;
use Laravel\Sanctum\PersonalAccessToken;
use function PHPUnit\Framework\assertNotNull;

class ChapterTest extends TestCase
{
    protected const array CHAPTER_RESPONSE = [
        'id',
        'title',
        'updates'
    ];

    protected const array CHAPTER_REQUEST = [
        'title' => 'test'
    ];

    public function testShowChapter(): void
    {
        $this->authenticateUser();
        $this->assertNotEmpty(PersonalAccessToken::all());

        /** @var Chapter $chapter */
        $chapter = Chapter::first();
        $response = $this->request(Uri::route('chapters.show', ['quest' => $chapter->quest_id ,'chapter' => $chapter->id]), 'GET');
        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => self::CHAPTER_RESPONSE]);
    }

    public function testShowChapterNotFoundBecauseUnlisted(): void
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::first();
        $chapter->quest()->update(['status' => QuestStatusEnum::UNLISTED->value]);
        $response = $this->request(Uri::route('chapters.show', ['quest' => $chapter->quest_id ,'chapter' => $chapter->id]), 'GET');
        $response->assertStatus(403);
    }

    public function testStoreChapter(): void
    {
        $this->authenticateUser();
        $this->assertNotEmpty(PersonalAccessToken::all());

        $quest = User::where('name', self::USER_DATA['name'])->first()->quests()->first();
        $this->assertNotNull($quest);

        $response = $this->request(Uri::route('chapters.store', ['quest' => $quest->id]), 'POST', self::CHAPTER_REQUEST);
        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => self::CHAPTER_RESPONSE]);
    }

    public function testStoreChapterNotFound(): void
    {
        $this->authenticateUser();
        $response = $this->request(Uri::route('chapters.store', ['quest' => 0]), 'POST', self::CHAPTER_REQUEST);
        $response->assertStatus(404);
    }

    public function testUpdateChapter(): void
    {
        $this->authenticateUser();
        $this->assertNotEmpty(PersonalAccessToken::all());

        $quest = User::where('name', self::USER_DATA['name'])->first()->quests()->first();
        $this->assertNotNull($quest);

        $response = $this->request(
            Uri::route('chapters.update', ['quest' => $quest->id, 'chapter' => $quest->chapters()->first()->id]),
            'PUT', self::CHAPTER_REQUEST
        );
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => self::CHAPTER_RESPONSE]);
    }

    public function testUpdateChapterNotFound(): void
    {
        $this->authenticateUser();
        $response = $this->request(Uri::route('chapters.store', ['quest' => 0]), 'POST', self::CHAPTER_REQUEST);
        $response->assertStatus(404);
    }
}
