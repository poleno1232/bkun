<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

abstract class Controller
{
    protected function ensureUserIsAuthenticated()
    {
        if (!auth()->user() && request()->bearerToken()) {
            Auth::setUser(
                PersonalAccessToken::findToken(explode('|', request()->bearerToken())[1])->tokenable
            );
        }
    }
}
