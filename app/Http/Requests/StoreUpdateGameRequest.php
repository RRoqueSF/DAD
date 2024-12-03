<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        
                // Custom validation for single-player games
                'board_size' => 'required_if:type,S|in:3x4,4x4,6x6', // Update this to validate board_size directly
            ];
        }
        
    /**
     * Custom messages for validation rules
     */
    public function messages()
    {
        return [
            'custom.board_size.required_if' => 'Board size is required for single-player games.',
            'custom.board_size.in' => 'Board size must be one of: 3x4, 4x4, 6x6.',
        ];
    }
}
