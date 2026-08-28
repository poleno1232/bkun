<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Users\LoginResource;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function login(LoginRequest $request): LoginResource
    {
        $credentials = $request->validated();
        $user = $this->userService->findByName($credentials['name']);

        $token = $user->createToken($user->name);

        return LoginResource::make(['user' => $user, 'token' => $token]);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = $this->userService->register($data);
        $token = $user->createToken($user->name);

        return LoginResource::make(['user' => $user, 'token' => $token]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me(): UserResource
    {
        return UserResource::make(\auth()->user());
    }

    public function refreshToken(): LoginResource
    {
        /** @var User $user */
        $user = \auth()->user();
        $token = $user->createToken($user->name, $user->permissions()->pluck('name')->toArray());

        return LoginResource::make(['user' => $user, 'token' => $token]);
    }
}
