<?php
namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $authUser, User $user)
    {
        return $authUser->is_admin || $authUser->id === $user->id;
    }

    public function update(User $authUser, User $user)
    {
        return $authUser->id === $user->id || $authUser->is_admin;
    }

    public function delete(User $authUser, User $user)
    {
        return $authUser->is_admin && $authUser->id !== $user->id;
    }

    public function createAdmin(User $authUser)
    {
        return $authUser->is_admin;
    }
}
