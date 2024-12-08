<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Http\Resources\AdminResource;
use App\Http\Requests\StoreAdminRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function store(StoreAdminRequest $request)
    {
        // Create a new admin using the validated data from the request
        $newAdmin = Admin::create($request->validated());
        $newAdmin->type = 'A';
        $newAdmin->save();
        // Return the newly created admin resource
        return new AdminResource($newAdmin);
    }
    

    public function destroy (User $user){

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Account deleted',
        ]);
    }
}