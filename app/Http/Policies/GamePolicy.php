<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Game;

class GamePolicy
{
    public function view(User $authUser, Game $game)
    {
        return $authUser->is_admin || $authUser->id === $game->created_user_id;
    }

    public function create(User $authUser)
    {
        return !$authUser->is_admin;
    }

    public function update(User $authUser, Game $game)
    {
        return $authUser->id === $game->created_user_id;
    }

    public function delete(User $authUser, Game $game)
    {
        return $authUser->id === $game->created_user_id;
    }
}
