<?php

namespace App\Services;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            /** @var User $user */
            $user = User::query()->create(Arr::only($data, ['name', 'email', 'password']));

            $this->syncProfile($user, $data);

            $user->load('profile');
            $this->dispatchSafely(new UserCreated($user));

            return $user;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $userData = Arr::only($data, ['name', 'email', 'password']);

            if (empty($userData['password'])) {
                unset($userData['password']);
            }

            $user->fill($userData)->save();

            $this->syncProfile($user, $data);

            $user->load('profile');
            $this->dispatchSafely(new UserUpdated($user));

            return $user;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $this->syncProfile($user, $data, force: true);

            $user->load('profile');
            $this->dispatchSafely(new UserUpdated($user, 'Профиль пользователя обновлен'));

            return $user;
        });
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncProfile(User $user, array $data, bool $force = false): void
    {
        $profileData = Arr::only($data, ['phone', 'address']);
        $hasProfileData = $force || Arr::hasAny($data, ['phone', 'address']);

        if (! $hasProfileData) {
            return;
        }

        $user->profile()->updateOrCreate(['user_id' => $user->id], [
            'phone' => $profileData['phone'] ?? null,
            'address' => $profileData['address'] ?? null,
        ]);
    }

    protected function dispatchSafely(object $event): void
    {
        try {
            event($event);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
