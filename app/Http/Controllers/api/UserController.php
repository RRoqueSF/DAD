<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function showMe(Request $request)
{
return new UserResource($request->user());
}


    // Show the authenticated user's profile
    public function show()
    {
        return response()->json([
            'status' => 'success',
            'data' => Auth::user(),
        ]);
    }

    // Update the authenticated user's profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'email|unique:users,email,' . $user->id,
            'nickname' => 'string|max:255|unique:users,nickname,' . $user->id,
            'password' => 'nullable|string|min:3',
            'photo_filename' => 'nullable|string',
            'name' => 'string|max:255',
        ]);

        $user->update($request->only('email', 'nickname', 'name', 'photo_filename'));

        if ($request->password) {
            $user->password = $request->password; // Automatically hashed in User model
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    // Delete the authenticated user's account
    public function destroy(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'confirmation' => 'required|string',
        ]);

        if (
            $request->confirmation !== $user->nickname &&
            !Hash::check($request->confirmation, $user->password)
        ) {
            return response()->json(['status' => 'error', 'message' => 'Invalid confirmation'], 403);
        }

        // Soft delete the user
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Account deleted',
        ]);
    }
}

