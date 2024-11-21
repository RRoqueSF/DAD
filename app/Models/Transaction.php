<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
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
        'transaction_datetime',
        'user_id',
        'game_id',
        'type',
        'euros',
        'brain_coins',
        'payment_type',
        'payment_reference',
        'custom',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_datetime' => 'datetime',
        'euros' => 'decimal:2',
        'custom' => 'json',
    ];

    /**
     * Relationships with other models.
     */

    // Relationship with the user who made the transaction
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with the game associated with the transaction, if any
    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}
