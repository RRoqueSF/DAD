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
    $user = auth()->user();

    // Decode the custom field if it is a JSON string
    $custom = $validated['custom'] ? json_decode($validated['custom'], true) : [];

    // Check if the user has enough brain coins for non-3x4 boards
    if ($custom['board_size'] !== '3x4' && $user->brain_coins_balance < 1) {
        return response()->json(['message' => 'You do not have enough brain coins to play on this board size.'], 403);
    }

    $boardSize = $custom['board_size'];
    switch ($boardSize) {
        case '3x4':
            $boardId = 1; // For 3x4, set board_id to 1
            break;
        case '4x4':
            $boardId = 2; // For 4x4, set board_id to 2
            break;
        case '6x6':
            $boardId = 3; // For 6x6, set board_id to 3
            break;
        default:
            return response()->json(['message' => 'Invalid board size'], 400);
    }


    // Check if the user has enough brain coins for boards other than 3x4
    if ($boardSize !== '3x4') {
        if ($user->brain_coins_balance < 1) {
            return response()->json(['message' => 'Insufficient brain coins.'], 403);
        }

        // Deduct 1 brain coin
        $user->decrement('brain_coins_balance', 1);

        // Log transaction
        Transaction::create([
            'transaction_datetime' => now(),
            'user_id' => $user->id,
            'game_id' => null, // This will be updated once the game is created
            'type' => 'P', // Payment
            'brain_coins' => -1,
            'euros' => 0,
            'custom' => json_encode(['reason' => 'Single-player game board fee']),
        ]);
    }

    // Create the game
    $game = Game::create([
        'created_user_id' => $user->id,
        'board_id' => $boardId,
        'type' => 'S', // Single-player game
        'status' => 'PE', // Pending
        'custom' => $validated['custom'], // Store custom game data (e.g., board size)
    ]);

    return response()->json([
        'message' => 'Game created successfully!',
        'game' => $game,
    ], 201);
}


    /**
     * End a single-player game
     */
    public function update(StoreUpdateGameRequest $request, Game $game)
    {
        $validated = $request->validated();

        // Ensure the game belongs to the authenticated user
        $user = auth()->user();
        if ($game->created_user_id !== $user->id) {
            return response()->json(['message' => 'You are not authorized to update this game.'], 403);
        }

        // End the game and calculate total time
        $validated['ended_at'] = now();
        if (isset($validated['began_at']) && $validated['ended_at']) {
            $validated['total_time'] = Carbon::parse($validated['ended_at'])->diffInSeconds(Carbon::parse($validated['began_at']));
        }

        $validated['status'] = 'E'; // Ended
        $game->update($validated);

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
