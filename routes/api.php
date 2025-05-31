<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ConversationsController;
use App\Http\Controllers\API\GradesController;
use App\Http\Controllers\API\MessagesController;
use App\Http\Controllers\API\PublicationsController;
use App\Http\Controllers\API\UserController;
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
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/publications/guest', [PublicationsController::class, 'guestFeed']);

Route::get('/user', [UserController::class, 'getCurrentUser']);
Route::get('/users/top', [UserController::class, 'getTop']);
Route::get('/users/last-week-top', [UserController::class, 'getLastWeekTop']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::get('/users/{user}/publications', [PublicationsController::class, 'getUserPublications']);

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/publications', [AdminController::class, 'getPublications']);
    Route::delete('/publications/{publication}', [AdminController::class, 'destroyPublication']);
    Route::patch('/publications/{id}/approve', [AdminController::class, 'approve']);
    Route::patch('/publications/{id}/reject', [AdminController::class, 'reject']);

    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser']);
});

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::apiResource('/publications', PublicationsController::class)->except('update');
    Route::post('publications/{publication}', [PublicationsController::class, 'update']);

    Route::apiResource('/conversations', ConversationsController::class)->except('update', 'create', 'edit');

    Route::apiResource('/messages', MessagesController::class)->only('store', 'destroy');
    Route::post('/messages/{message}', [MessagesController::class, 'update']);

    Route::post('/users/{user}', [UserController::class, 'update']);

    Route::post('/publications/{publication}/like', [GradesController::class, 'like']);
    Route::post('/publications/{publication}/dislike', [GradesController::class, 'dislike']);
    Route::patch('/publications/{publication}/grade', [GradesController::class, 'update']);
    Route::delete('publications/{publication}/grade', [GradesController::class, 'destroy']);
});
