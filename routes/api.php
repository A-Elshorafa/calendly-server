<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\UserEventController;
use App\Http\Controllers\ThirdParityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/getAuthUserInfo', [UserController::class, 'getAuthUserInfo'])->middleware(['auth:sanctum']);

/// user events endpoints \\\
Route::get('getPendingEventDetails', [UserEventController::class, 'getPendingEventDetails']);
Route::post('storeEvent', [UserEventController::class, 'store'])->middleware('auth:sanctum');
Route::delete('deleteEvent', [UserEventController::class, "deleteEvent"])->middleware('auth:sanctum');
Route::get('getPendingEvents', [UserEventController::class, 'getPendingEvents'])->middleware('auth:sanctum');
Route::post('updateEventNotes', [UserEventController::class, 'updateEventNotes'])->middleware('auth:sanctum');
Route::get('getUpComingEvents', [UserEventController::class, 'getUpComingEvents'])->middleware(['auth:sanctum']);
Route::get('getUpcomingEventDetails', [UserEventController::class, 'getUpcomingEventDetails'])->middleware('auth:sanctum');

/// attendee endpoints \\\
Route::post('subscribeToEvent', [AttendeeController::class, 'subscribeToEvent']);

//// third-party \\\\
Route::get('/getUserInfo', [ThirdParityController::class, 'getUserInfo'])->middleware('auth:sanctum');
Route::get('/authorizeUser', [ThirdParityController::class, "authorizeUser"])->middleware('auth:sanctum');
Route::get('/setUserTokens', [ThirdParityController::class, "storeUserTokens"])->middleware('auth:sanctum');
Route::get('/refreshAccessToken', [ThirdParityController::class, 'refreshAccessToken'])->middleware('auth:sanctum');