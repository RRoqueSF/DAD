<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;
use App\Http\Requests\StoreUpdateGameRequest;
use Illuminate\Support\Carbon;

class GameController extends Controller
{
    /**
     * List all games
     */
    public function index()
{
    // Get the authenticated user
    $user = auth()->user();
    
    // If the user is not authenticated, return an error response
    if (!$user) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

   
    $games = Game::where('created_user_id', $user->id)
                 ->with(['creator', 'winner', 'board']) 
                 ->get();

    // Return the games as a collection of GameResource
    return GameResource::collection($games);
}

    /**
     * Show details of a single game
     */
    public function show(Game $game)
    {
        $game->load(['createdUser', 'winnerUser', 'board']);
        return new GameResource($game);
    }

    /**
     * Store a new game
     */
    public function store(StoreUpdateGameRequest $request)
    {
        $validated = $request->validated();

        // Ensure `ended_at` is null unless status is 'E' (ended)
        if ($validated['status'] !== 'E') {
            $validated['ended_at'] = null;
        }

        // Calculate total_time if game has ended
        if ($validated['status'] === 'E' && isset($validated['began_at']) && isset($validated['ended_at'])) {
            $validated['total_time'] = Carbon::parse($validated['ended_at'])->diffInSeconds(Carbon::parse($validated['began_at']));
        }

        // Set default timestamps
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        $game = Game::create($validated);

        return new GameResource($game);
    }

    /**
     * Update an existing game
     */
    public function update(StoreUpdateGameRequest $request, Game $game)
    {
        $validated = $request->validated();

        // Ensure `ended_at` is null unless status is 'E' (ended)
        if ($validated['status'] !== 'E') {
            $validated['ended_at'] = null;
        }

        // Calculate total_time if game has ended
        if ($validated['status'] === 'E' && isset($validated['began_at']) && isset($validated['ended_at'])) {
            $validated['total_time'] = Carbon::parse($validated['ended_at'])->diffInSeconds(Carbon::parse($validated['began_at']));
        }

        $validated['updated_at'] = now(); // Update timestamp

        $game->fill($validated);
        $game->save();

        return new GameResource($game);
    }

    /**
     * Delete a game
     */
    public function destroy(Game $game)
    {
        $game->delete();
        return response()->json(null, 204);
    }
}
