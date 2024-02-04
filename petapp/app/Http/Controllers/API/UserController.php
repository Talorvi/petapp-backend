<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AvatarService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function showAuthenticatedUser(): JsonResponse
    {
        $user = \Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], ResponseAlias::HTTP_NOT_FOUND);
        }
        return response()->json($user);
    }

    public function index(): JsonResponse
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->userService->updateUser($request->validated());
        return response()->json($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
