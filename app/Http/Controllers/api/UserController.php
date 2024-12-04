<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUpdateUserRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserDeleteRequest;

class UserController extends Controller
{
    public function showMe(Request $request)
{
return new UserResource($request->user());
}
public function index(Request $request){

    $userType = $request->userType;
    $userQuery = User::query();

    if ($userType != null)
        $userQuery->where('user_type', $userType);

    if ($request->paginate == '0')
        return UserResource::collection($userQuery->orderBy('name', 'asc')->get());

    $blocked = $request->blocked;
    $order = $request->order;

    if ($blocked != null)
        $userQuery->where('blocked', $blocked);

    if ($order != null)
        $userQuery->orderBy('name', $order);
    
    
    return UserResource::collection($userQuery->paginate(15));
}
public function update_password (UpdatePasswordRequest $request, User $user)
{
    $user->password = Hash::make($request->validated()['password']);
    $user->save();
    return new UserResource($user);
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
    
    


public function update(StoreUpdateUserRequest $request, User $user)
{
    // Update the user details
    $user->fill($request->validated());

    if ($request->hasFile('photo')) {
        // Check if the user already has a photo and delete the old one if exists
        if ($user->photo_filename) {
            Storage::delete($user->photo_filename);
        }
    
        if ($request->hasFile('photo')) {
            // If the user already has a photo, delete it
            if ($user->photo_filename) {
                Storage::delete('public/photos/' . $user->photo_filename);
            }

            // Store the new photo and update the user record
            $path = $request->file('photo')->store('public/photos/');
            $user->photo_filename = basename($path); // Save the file name
            $user->save();
        }
    }
    // Save the updated user data
    $user->save();

    // Return the updated user data, including the photo URL
    return response()->json([
        'message' => 'Profile updated successfully.',
        'photo_url' => asset(Storage::url($user->photo_filename)), // Return the URL for the uploaded photo
    ]);
}



    // Delete the authenticated user's account
    public function destroy(UserDeleteRequest $request, User $user)
    {

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

