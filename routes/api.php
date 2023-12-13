<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Chats
use App\Http\Controllers\GroupChatController;
use App\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Chats
// Group Chat Routes
Route::prefix('group-chats')->group(function () {
    Route::get('/', [GroupChatController::class, 'index']);
    Route::get('/{groupChat}', [GroupChatController::class, 'show']);
    // Add other routes for CRUD operations as needed
});
