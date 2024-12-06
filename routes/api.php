<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::post('/logout', [ApiController::class, 'logout']);
});
