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
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $games = Game::where('created_user_id', $user->id)
            ->with(['creator', 'winner', 'board'])
            ->get();

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

    public function store(StoreUpdateGameRequest $request)
    {
        $validated = $request->validated();
    
        // Create the game record
        $game = Game::create($validated);
    
        return response()->json([
            'message' => 'Game created successfully!',
            'game' => $game,
        ], 201);
    }
    public function update(StoreUpdateGameRequest $request, Game $game)
{
    $validated = $request->validated();

    $game->update($validated);

    return response()->json([
        'message' => 'Game updated successfully!',
        'game' => $game,
    ]);
}

    /**
     * Delete a game
     */
    public function destroy(Game $game)
    {
        $game->delete();
        return response()->json(null, 204);
    }

    /**
     * Start a new single-player game
     */
    public function startSinglePlayerGame(StoreUpdateGameRequest $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validate([
            'board_size' => 'required|string', // For example, '4x4', '5x5', etc.
        ]);

        $game = Game::create([
            'created_user_id' => $user->id,
            'board_size' => $validated['board_size'],
            'status' => 'I', // I = In Progress
            'began_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return new GameResource($game);
    }

    /**
     * End an existing single-player game
     */
    public function endSinglePlayerGame(Request $request, Game $game)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($game->created_user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'game_time' => 'required|integer', // Total time in seconds
        ]);

        $game->status = 'E'; // E = Ended
        $game->ended_at = now();
        $game->total_time = $validated['game_time'];
        $game->updated_at = now();

        $game->save();

        return new GameResource($game);
    }
}
