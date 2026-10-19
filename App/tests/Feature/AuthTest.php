<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Uri;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpKernel\Exception\HttpException;
use function PHPUnit\Framework\assertEmpty;

class AuthTest extends TestCase
{
    protected const LOGIN_RESPONSE = [
        'data' => [
            'user' => [
                'name',
                'email',
            ],
            'token',
        ],
    ];

    protected const REGISTER_REQUEST = [
        'name' => 'Test_User',
        'password' => 'password',
        'email' => 'test2@example.com',
        'password_confirmation' => 'password',
    ];

    protected const REGISTER_INCORRECT_REQUEST = [

        'name' => 'Test User',
        'password' => 'password',
        'email' => 'test',
        'password_confirmation' => 'password1',
    ];
    /**
     * A basic feature test example.
     */
    public function testAssertLogin(): void
    {
        $response = $this->authenticateAdmin();
        $this->assertNotEmpty(PersonalAccessToken::all());

        $response->assertStatus(200);
        $response->assertJsonStructure(self::LOGIN_RESPONSE);
    }

    public function testAssertLogout(): void
    {
        $this->authenticateAdmin();
        $this->assertNotEmpty(PersonalAccessToken::all());

        $response = $this->request(Uri::route('logout'), 'post');

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertEmpty(PersonalAccessToken::all());
    }

    public function testAssertLoginWithInvalidCredentials(): void
    {
        $response = $this->request(Uri::route('login'), 'post', self::INCORRECT_USER_DATA);
        $response->assertStatus(401);
    }

    public function testRegisterSuccess(): void
    {
        $response = $this->request(Uri::route('register'), 'post', data: self::REGISTER_REQUEST);
        $this->assertNotEmpty(PersonalAccessToken::all());

        $response->assertStatus(200);
        $response->assertJsonStructure(self::LOGIN_RESPONSE);
        $this->assertNotEmpty(User::where('email', self::REGISTER_REQUEST['email'])->first());
    }

    public function testRegisterWithInvalidCredentials(): void
    {
        $response = $this->request(Uri::route('register'), 'post', data: self::REGISTER_INCORRECT_REQUEST);
        $this->assertEmpty(PersonalAccessToken::all());

        $response->assertStatus(422);
        $response->assertJsonStructure(self::UNPROCESSABLE_ENTITY_RESPONSE);
        $this->assertEmpty(User::where('email', self::REGISTER_REQUEST['email'])->first());
    }

    public function testMeRequest()
    {
        $this->authenticateAdmin();
        $this->assertNotEmpty(PersonalAccessToken::all());

        $response = $this->request(Uri::route('me'), 'get');
        $response->assertStatus(200);
        $response->assertJsonStructure(self::USER_RESPONSE);
    }

    public function testMeRequestUnauthorized()
    {
        $response = $this->request(Uri::route('me'), 'get');
        $response->assertStatus(401);
    }

    public function testTokenRefresh(): void
    {
        $this->authenticateAdmin();
        $tokens = PersonalAccessToken::count();
        $this->assertGreaterThan(0, $tokens);

        $response = $this->request(Uri::route('refreshToken'), 'post',);
        $response->assertStatus(200);
        $response->assertJsonStructure(self::LOGIN_RESPONSE);
        $this->assertGreaterThan($tokens, PersonalAccessToken::count());
    }

    public function testTokenRefreshUnauthorized()
    {
        $response = $this->request(Uri::route('refreshToken'), 'post');
        $response->assertStatus(401);
    }
}
