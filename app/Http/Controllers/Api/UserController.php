<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function index(UserIndexRequest $request)
    {
        $data = $request->validated();

        $users = User::query()
            ->with('profile')
            ->latest('id')
            ->paginate($data['per_page'] ?? 10)
            ->withQueryString();

        return UserResource::collection($users);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $user): UserResource
    {
        $user->load('profile');

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $user = $this->userService->update($user, $request->validated());

        return new UserResource($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return response()->json(status: 204);
    }
}
