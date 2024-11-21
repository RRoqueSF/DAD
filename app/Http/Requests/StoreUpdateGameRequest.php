<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateGameRequest extends FormRequest
{
    /**
     * Authorize the request
     */
    public function authorize()
    {
        return true; // Set appropriate authorization logic
    }

    /**
     * Validation rules for the game
     */
    public function rules()
    {
        return [
            'type' => 'required|in:S,M', // Single or Multiplayer
            'created_user_id' => 'required|exists:users,id',
            'winner_user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:PE,PL,E,I', // Pending, In Progress, Ended, Interrupted
            'began_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:began_at',
            'total_time' => 'nullable|numeric|min:0',
            'board_id' => 'nullable|exists:boards,id',
            'custom' => 'nullable|json',
        ];
    }
}
