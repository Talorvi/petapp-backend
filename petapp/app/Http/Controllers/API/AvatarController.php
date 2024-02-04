<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Avatar\UpdateUserAvatarRequest;
use App\Services\AvatarService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    use HasUuids;

    private AvatarService $avatarService;

    public function __construct(AvatarService $avatarService)
    {
        $this->avatarService = $avatarService;
    }

    public function updateAvatar(UpdateUserAvatarRequest $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => __('messages.user_not_found')], 404);
        }

        if ($request->hasFile('avatar')) {
            if ($this->avatarService->updateAvatar($user, $request->file('avatar'))) {
                return response()->json(['message' => __('messages.avatar_updated_successfully')]);
            } else {
                return response()->json(['message' => __('messages.an_error_occurred_while_uploading_the_avatar')], 500);
            }
        } else {
            return response()->json(['message' => __('messages.no_avatar_file_provided')], 400);
        }
    }

    public function removeAvatar(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => __('messages.user_not_found')], 404);
        }

        if ($this->avatarService->removeAvatar($user)) {
            return response()->json(['message' => __('messages.avatar_removed_successfully')]);
        } else {
            return response()->json(['message' => __('messages.an_error_occurred_while_removing_the_avatar')], 500);
        }
    }
}
