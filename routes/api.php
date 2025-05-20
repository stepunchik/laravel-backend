<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PublicationsController;
use App\Http\Controllers\API\GradesController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ConversationsController;
use App\Http\Controllers\API\MessagesController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signup']);

Route::get('/publications/guest', [PublicationsController::class, 'guestFeed']);

Route::get('/users/top', [UserController::class, 'getTop']);
Route::get('/users/last-week-top', [UserController::class, 'getLastWeekTop']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::get('/publications/{user}', [PublicationsController::class, 'getUserPublications']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/publications', PublicationsController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('/conversations', ConversationsController::class)->except('update', 'create', 'edit');

    Route::apiResource('/messages', MessagesController::class)->only('store', 'destroy');
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/publications/{publication}/like', [GradesController::class, 'like']);
    Route::post('/publications/{publication}/dislike', [GradesController::class, 'dislike']);
    Route::patch('/publications/{publication}/grade', [GradesController::class, 'update']);
    Route::delete('publications/{publication}/grade', [GradesController::class, 'destroy']);
});



