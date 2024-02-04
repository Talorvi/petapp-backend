<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AvatarService
{
    public function updateAvatar(User $user, UploadedFile $file): bool
    {
        try {
            $user->clearMediaCollection('avatars');
            $randomFileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $user->addMedia($file)
                ->usingFileName($randomFileName)
                ->toMediaCollection('avatars');
            return true;
        } catch (\Exception $e) {
            Log::error('An error occurred while uploading the avatar: ' . $e->getMessage());
            return false;
        }
    }

    public function removeAvatar(User $user): bool
    {
        try {
            $user->clearMediaCollection('avatars');
            return true;
        } catch (\Exception $e) {
            Log::error('An error occurred while removing the avatar: ' . $e->getMessage());
            return false;
        }
    }
}
