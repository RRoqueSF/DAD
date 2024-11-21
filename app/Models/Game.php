<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'created_user_id',
        'winner_user_id',
        'board_id',
        'type',
        'status',
        'board_id',
        'custom',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'began_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'total_time' => 'decimal:2',
        'custom' => 'json',
    ];

    /**
     * Relationships with other models.
     */

    // Relationship with the user who created the game
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    // Relationship with the user who won the game
    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    // Relationship with the board used in the game
    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id');
    }

    // Relationship with multiplayer games played
    public function multiplayerGamesPlayed()
    {
        return $this->hasMany(MultiplayerGamePlayed::class, 'game_id');
    }

    // Relationship with transactions associated with the game
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'game_id');
    }
}
