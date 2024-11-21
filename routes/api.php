<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\GameController;


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);
    Route::get('/users/me', [UserController::class , 'showMe']);
    Route::get('/user', [UserController::class, 'show'])->name('user.show');       
    Route::put('/user', [UserController::class, 'update'])->name('user.update');  
    Route::delete('/user', [UserController::class, 'destroy'])->name('user.destroy');


    Route::get('/games/{created_user_id}', [GameController::class, 'index']);
    Route::get('/games/{game}', [GameController::class, 'show']);
    Route::post('/games', [GameController::class, 'store']);
    Route::put('/games/{game}', [GameController::class, 'update']);
    Route::delete('/games/{game}', [GameController::class, 'destroy']);
});
Route::post('/auth/login', [AuthController::class, 'login']);