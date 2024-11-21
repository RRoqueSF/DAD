<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    private function purgeExpiredTokens()
    {
        // Only deletes if token expired 2 hours ago
        $dateTimetoPurge = now()->subHours(2);
        DB::table('personal_access_tokens')
            ->where('expires_at', '<', $dateTimetoPurge)->delete();
    }

    private function revokeCurrentToken(User $user)
    {
        $currentTokenId = $user->currentAccessToken()->id;
        $user->tokens()->where('id', $currentTokenId)->delete();
    }

    public function login(LoginRequest $request)
    {
        $this->purgeExpiredTokens();
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = $request->user()->createToken('authToken', ['*'], now()->addHours(2))->plainTextToken;
        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        $this->purgeExpiredTokens();
        $this->revokeCurrentToken($request->user());
        return response()->json(null, 204);
    }

    public function refreshToken(Request $request)
    {
        // Revokes current token and creates a new token
        $this->purgeExpiredTokens();
        $this->revokeCurrentToken($request->user());
        $token = $request->user()->createToken('authToken', ['*'], now()->addHours(2))->plainTextToken;
        return response()->json(['token' => $token]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'nickname' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:3',
            'photo_filename' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nickname' => $request->nickname,
            'password' => $request->password, // Automatically hashed in the model
            'photo_filename' => $request->photo_filename,
            'brain_coins_balance' => 0,
            'blocked' => false,
            'type' => 'P',
        ]);

       $token = $user->createToken('authToken', ['*'], now()->addHours(2))->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => ['user' => $user, 'token' => $token],
        ], 201);
    }
}