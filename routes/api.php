<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

/// user events endpoints \\\
// todo use auth middleware
Route::post('storeEvent', [UserEventController::class, 'store']);
Route::get('getEventInfo', [UserEventController::class, 'getEventInfo']);
Route::get('getPendingEvents', [UserEventController::class, 'getPendingEvents']);
Route::get('getUpComingEvents', [UserEventController::class, 'getUpComingEvents']);

/// attendee endpoints \\\
Route::post('addAttendee', [AttendeeController::class, 'addAttendee']);

//// third-party \\\\

Route::get('/authorizeUser', [ThirdParityController::class, "authorizeUser"]);

Route::get('/setUserTokens', [ThirdParityController::class, "storeUserTokens"])->middleware('auth:sanctum');
Route::get('/refreshAccessToken', [ThirdParityController::class, 'refreshAccessToken'])->middleware('auth:sanctum');
Route::get('/getUserInfo', [ThirdParityController::class, 'getUserInfo'])->middleware('auth:sanctum');