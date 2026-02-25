<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use App\Services\UserService;

class UserProfileController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function show(User $user): ProfileResource
    {
        $user->load('profile');
        abort_if(! $user->profile, 404, 'Профиль не найден');

        return new ProfileResource($user->profile);
    }

    public function update(ProfileUpdateRequest $request, User $user): ProfileResource
    {
        $user = $this->userService->updateProfile($user, $request->validated());

        return new ProfileResource($user->profile);
    }
}
