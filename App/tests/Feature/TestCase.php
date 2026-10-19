<?php

namespace Tests\Feature;

use App\Enums\QuestStatusEnum;
use App\Models\Chapter;
use App\Models\ChatMessage;
use App\Models\Quest;
use App\Models\SubmissionUpdate;
use App\Models\TextUpdate;
use App\Models\User;
use App\Models\VoteUpdate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Uri;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Junges\Kafka\Facades\Kafka;
use Symfony\Component\HttpKernel\Exception\HttpException;
use function Laravel\Prompts\number;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use RefreshDatabase;

    protected const array ADMIN_DATA = [
        'name' => 'admin',
        'email' => 'test@example.com',
        'password' => 'password',
    ];

    protected const array USER_DATA = [
        'name' => 'user',
        'email' => 'user@example.com',
        'password' => 'password',
    ];

    protected const array ANOTHER_USER_DATA = [
        'name' => 'user1',
        'email' => 'user1@example.com',
        'password' => 'password',
    ];

    protected const array INCORRECT_USER_DATA = [
        'name' => 'user',
        'email' => 'user@example.com',
        'password' => 'password1_test',
    ];

    protected const array UNPROCESSABLE_ENTITY_RESPONSE = [
        'message',
        'errors' => [
            '*' => ['*' => []],
        ],
    ];

    protected const array USER_RESPONSE = [
        'data' => [
            'id',
            'name',
            'email',
        ],
    ];

    protected const array USER_SUBRESPONSE = [
        'user' => [
            'id',
            'name',
            'email',
        ]
    ];

    protected const array CHAPTER_SUBRESPONSE = [
        'chapters' => [
            '*' => [
                'id',
                'title',
                'updates',
            ],
        ]
    ];

    protected ?string $token = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareUsers();
        $this->prepareQuest();
        Exceptions::fake();
        $this->clearToken();

        Kafka::fake();
    }

    protected function prepareUsers()
    {
        User::create([
            'name' => self::ADMIN_DATA['name'],
            'email' => self::ADMIN_DATA['email'],
            'password' => self::ADMIN_DATA['password'],
        ]);
        User::create([
            'name' => self::USER_DATA['name'],
            'email' => self::USER_DATA['email'],
            'password' => self::USER_DATA['password'],
        ]);
        User::create([
            'name' => self::ANOTHER_USER_DATA['name'],
            'email' => self::ANOTHER_USER_DATA['email'],
            'password' => self::ANOTHER_USER_DATA['password'],
        ]);
    }

    protected function prepareQuest()
    {
        $quest = Quest::create([
            'name' => 'Test Quest',
            'description' => 'Test Quest',
            'image_urp' => null,
            'status' => QuestStatusEnum::ACTIVE->value,
            'user_id' => User::where('email', self::USER_DATA['email'])->first()->id,
        ]);

        $chapter = Chapter::create([
            'title' => 'Test Chapter',
            'quest_id' => $quest->id,
        ]);

        TextUpdate::create([
            'chapter_id' => $chapter->id,
            'text' => 'Test Text',
            'posted_at' => now(),
        ]);

        VoteUpdate::create([
            'chapter_id' => $chapter->id,
            'posted_at' => now(),
        ]);

        SubmissionUpdate::create([
            'chapter_id' => $chapter->id,
            'posted_at' => now(),
        ]);

        for ($i=0; $i<5; $i++) {
            ChatMessage::create([
                'quest_id' => $quest->id,
                'user_id' => User::where('email', self::USER_DATA['email'])->first()->id,
                'ip' => '127.0.0.1',
                'message' => "Test Message $i",
                'posted_at' => now(),
            ]);
        }
    }

    protected function authenticateAdmin()
    {
        $response = $this->post(Uri::route('login'), [
            'name' => self::ADMIN_DATA['name'],
            'password' => self::ADMIN_DATA['password'],
        ]);
        $this->token = $response->json('data.token');

        return $response;
    }

    protected function authenticateUser()
    {
        $response = $this->post(Uri::route('login'), [
            'name' => self::USER_DATA['name'],
            'password' => self::USER_DATA['password'],
        ]);
        $this->token = $response->json('data.token');

        return $response;
    }

    protected function authenticateAnotherUser()
    {
        $response = $this->post(Uri::route('login'), [
            'name' => self::ANOTHER_USER_DATA['name'],
            'password' => self::ANOTHER_USER_DATA['password'],
        ]);
        $this->token = $response->json('data.token');

        return $response;
    }

    protected function clearToken()
    {
        $this->token = null;
        Auth::logout();
    }

    protected function request(Uri $route, string $method, array $data = [], array $params = []): TestResponse
    {
        $request = $this
            ->withoutExceptionHandling([
                HttpException::class,
                ValidationException::class,
                AuthenticationException::class,
                AuthorizationException::class,
                ModelNotFoundException::class
            ])
            ->withHeaders(
                array_merge(
                    ['Accept' => 'application/json'],
                    $this->defaultHeaders
                )
            );
        $method = strtolower($method);
        $this->token ? $request = $request->withToken($this->token) : '';

        return match ($method) {
            'get' => $request->get($route->toString() . '?' . http_build_query($params)),
            'post' => $request->post($route, $data),
            'put' => $request->put($route, $data),
        };
    }
}
