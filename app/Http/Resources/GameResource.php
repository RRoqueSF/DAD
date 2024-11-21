<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'created_user_id' => $this->created_user_id,
            'winner_user_id' => $this->winner_user_id,
            'status' => $this->status,
            'began_at' => $this->began_at,
            'ended_at' => $this->ended_at,
            'total_time' => $this->total_time,
            'board_id' => $this->board_id,
            'custom' => $this->custom,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
           'creator' => $this->whenLoaded('creator', function () {
        return [
            'id' => $this->creator->id,
            'name' => $this->creator->name,
        ];
        }),
        'winner' => $this->whenLoaded('winner', function () {
        return [
            'id' => $this->winner->id,
            'name' => $this->winner->name,
        ];
        }),

            'board' => $this->whenLoaded('board'),
        ];
    }
}
