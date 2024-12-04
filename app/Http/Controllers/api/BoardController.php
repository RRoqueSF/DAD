<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Board;

class BoardController extends Controller{

    public function index(Request $request){
        return response()->json([
            'data' => Board::select('id', 'board_cols', 'board_rows')->get()
        ]);
    }
        

    public function show(Board $board){
        return new BoardResource($board);
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'user_id' => 'required|integer',
        ]);
        $board = new Board();
        $board->fill($request->all());
        $board->save();
        return new BoardResource($board);
    }
    public function update(Request $request, Board $board){
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'user_id' => 'required|integer',
        ]);
        $board->fill($request->all());
        $board->save();
        return new BoardResource($board);
    }
    public function destroy(Board $board){
        $board->delete();
        return response()->json(null, 204);
    }

}

