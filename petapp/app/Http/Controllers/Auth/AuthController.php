<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->all());
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], ResponseAlias::HTTP_CREATED);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $isLoginSuccess = $this->userService->loginUser($request->only(['email', 'password']));

        if ($isLoginSuccess) {
            return response()->json(['token' => $isLoginSuccess]);
        } else {
            return response()->json([
                'error' => __('messages.invalid_credentials')
            ], ResponseAlias::HTTP_UNAUTHORIZED);
        }
    }
}
