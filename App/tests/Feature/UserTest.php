<?php

namespace Tests\Feature;

use App\Models\BlockedUserQuest;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Support\Uri;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testShowSuccess(): void
    {
        $response = $this->request(Uri::route('users.show', ['user' => User::firstOrFail()->id]), 'get');

        $response->assertStatus(200);
        $response->assertJsonStructure(self::USER_RESPONSE);
    }

    public function testShowNotFound(): void
    {
        $response = $this->request(Uri::route('users.show', ['user' => 0]), 'get');

        $response->assertStatus(404);
    }

    public function testBlockAndUnblockUserInQuest(): void
    {
        $this->authenticateUser();
        $quest = Quest::where(
            'user_id',
            User::where('name', self::USER_DATA['name'])->firstOrFail()->id
        )->firstOrFail();
        $bannedUser = User::where('name', self::ANOTHER_USER_DATA['name'])->firstOrFail();

        $response = $this->request(
            Uri::route('quests.block.user', ['user' => $bannedUser->id, 'quest' => $quest->id]),
            'post'
        );
        $response->assertStatus(204);
        $this->assertNotNull(
            BlockedUserQuest::where('user_id', $bannedUser->id)->where('quest_id', $quest->id)
                ->first()
        );

        $response = $this->request(
            Uri::route('quests.unblock.user', ['user' => $bannedUser->id, 'quest' => $quest->id]),
            'post'
        );
        $response->assertStatus(204);
        $this->assertNull(
            BlockedUserQuest::where('user_id', $bannedUser->id)->where('quest_id', $quest->id)
                ->first()
        );
    }

    public function testBlockAndUnblockUserFailWrongUser(): void
    {
        $this->authenticateAnotherUser();
        $quest = Quest::where(
            'user_id',
            User::where('name', self::USER_DATA['name'])->firstOrFail()->id
        )->firstOrFail();
        $bannedUser = User::where('name', self::ANOTHER_USER_DATA['name'])->firstOrFail();

        $response = $this->request(
            Uri::route('quests.block.user', ['user' => $bannedUser->id, 'quest' => $quest->id]),
            'post'
        );
        $response->assertStatus(403);
        $this->assertNull(
            BlockedUserQuest::where('user_id', $bannedUser->id)->where('quest_id', $quest->id)
                ->first()
        );
    }
}
