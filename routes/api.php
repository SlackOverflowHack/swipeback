<?php

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

Route::prefix('user')->middleware('session', 'throttle:clientApp')->group(function() {

    Route::post('register', [UsersController::class, 'register']);

    Route::middleware('auth:fireuser')->group(function() {
        Route::get('/id', function (Request $request) {
            return $request->session()->get('userID');
        });
    });
});