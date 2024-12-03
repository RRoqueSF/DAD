<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUpdateUserRequest;
use App\Http\Requests\RegisterRequest;

class UserController extends Controller
{
    public function showMe(Request $request)
{
return new UserResource($request->user());
}


    // Show the authenticated user's profile
    public function show(request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => Auth::user(),
        ]);
    }

    public function store(RegisterRequest $request)
{
    // Hash the password
    $hashedPassword = Hash::make($request->input('password'));

    // Create the user with the hashed password
    $user = User::create([
        'name' => $request->input('name'),
        'nickname' => $request->input('nickname'),
        'email' => $request->input('email'),
        'password' => $hashedPassword, // Use the hashed password here
        'photo_url' => $request->input('photoUrl'),
    ]);

    // Return a response indicating success
    return response()->json([
        'message' => 'User successfully registered!',
        'data' => $user,
    ]);
}
    
    
    // Update the authenticated user's profile
    public function update(StoreUpdateUserRequest $request, User $user)
{
    $user->fill($request->validated());
    $user->save();

    
    return new UserResource($user);
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

