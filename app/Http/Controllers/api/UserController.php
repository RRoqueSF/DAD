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
use App\Services\Base64Services;

class UserController extends Controller
{
    public function showMe(Request $request)
{
return new UserResource($request->user());
}

private function storeBase64AsFile(User $user, String $base64String)
    {
        $targetDir = storage_path('app/public/photos');
        $newfilename = $user->id . "_" . rand(1000,9999);
        $base64Service = new Base64Services();
        return $base64Service->saveFile($base64String, $targetDir, $newfilename);
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
    
        // Initialize data for user creation
        $dataToSave = $request->validated();
        $base64ImagePhoto = $dataToSave["base64ImagePhoto"] ?? null;
        unset($dataToSave["base64ImagePhoto"]);
    
        // Add hashed password to the data
        $dataToSave['password'] = $hashedPassword;
    
        // Create the user
        $user = User::create($dataToSave);
    
        // Handle the photo if provided
        if ($base64ImagePhoto) {
            $user->photo_filename = $this->storeBase64AsFile($user, $base64ImagePhoto);
            $user->save();
        }
    
        // Return a response indicating success
        return response()->json([
            'message' => 'User successfully registered!',
            'data' => $user,
        ]);
    }
    
    
    


public function update(StoreUpdateUserRequest $request, User $user)
{
    $dataToSave = $request->validated();

    $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
        $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
    $deletePhotoOnServer = array_key_exists("deletePhotoOnServer", $dataToSave) && $dataToSave["deletePhotoOnServer"];
    unset($dataToSave["base64ImagePhoto"]);
    unset($dataToSave["deletePhotoOnServer"]);

    $user->fill($dataToSave);


    if ($user->photo_filename && ($deletePhotoOnServer || $base64ImagePhoto)) {
        if (Storage::exists('public/fotos/' . $user->photo_filename)) {
            Storage::delete('public/fotos/' . $user->photo_filename);
        }
        $user->photo_filename = null;
    }

    if ($base64ImagePhoto) {
        $user->photo_filename = $this->storeBase64AsFile($user, $base64ImagePhoto);
    }

    $user->save();
    return new UserResource($user);
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

