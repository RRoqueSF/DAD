<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, AuthenticatableTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'type',
        'nickname',
        'password',
        'blocked',
        'brain_coins_balance',
        'photo_filename',
        'custom',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */


    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'custom' => 'json',
        ];
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function createdGames()
    {
        return $this->hasMany(Game::class, 'created_user_id');
    }

    public function wonGames()
    {
        return $this->hasMany(Game::class, 'winner_user_id');
    }

    public function multiplayerGamesPlayed()
    {
        return $this->hasMany(MultiplayerGamePlayed::class, 'user_id');
    }
}
