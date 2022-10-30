<?php

use App\Http\Controllers\CoursesController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::middleware('throttle:clientApp')->post('login', 'App\Http\Controllers\ApiController@createApiToken');
Route::post('/user/register', [UsersController::class, 'register']);

Route::middleware('session', 'auth:fireuser', 'throttle:clientApp')->group(function() {
    Route::get('/', function(Request $request) {
        return $request->user();
    });

    Route::prefix('user')->group(function() {
        Route::post('update', [UsersController::class, 'update']);
    });
    Route::prefix('course')->group(function() {
        Route::post('addInterestedMember', [CoursesController::class, 'addInterestedMember']);
        Route::post('signupMember', [CoursesController::class, 'signupMember']);
        Route::post('removeInterestedMember', [CoursesController::class, 'removeInterestedMember']);
    });
});
