<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultiplayerGamePlayed extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'game_id',
        'player_won',
        'pairs_discovered',
        'custom',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'player_won' => 'boolean',
        'custom' => 'json',
    ];

    /**
     * Relationships with other models.
     */

    // Relationship with the user who played the game
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with the game that was played
    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}
