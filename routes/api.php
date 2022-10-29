<?php

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

Route::prefix('user')->middleware('auth:api', 'throttle:clientApp')->group(function() {
        Route::get('/', function (Request $request) {
                return $request->user();
        });

	Route::post('createHomeToken', 'App\Http\Controllers\ApiController@createHomeToken');
});


/**
 * routes for lamaframe integration (only from frame)
 */
Route::prefix('frame')->middleware('auth:frameDevice', 'throttle:frameDevice')->group(function(){
	Route::get('image/{imageID}', 'App\Http\Controllers\ImagesController@getImage');
	Route::get('imageIDs', 'App\Http\Controllers\FramesController@getImageIDs');
});

/**
 * routes from alexa / bixby
 * oauth will be used for authentication
 * client user (not alexa etc) is authenticated
 */
Route::prefix('integrations')->middleware(['auth:api', 'scope:devices-read'])->group(function() {
	Route::prefix('alexa')->group(function() {
		Route::get('discover', 'App\Http\Controllers\Integrations\AlexaController@handleDiscoveryRequest');
		Route::post('stateRefresh', 'App\Http\Controllers\Integrations\AlexaController@handleStateRefreshRequest');
		Route::middleware('scope:devices-set')->post('setState', 'App\Http\Controllers\Integrations\AlexaController@handleSetStateRequest');
	});

	Route::post('smartthings', 'App\Http\Controllers\SmartThingsController@receiveMessage');
});

/// route for moving the token from a SmartThings response into the header. message will be resent
Route::post('integrations/smartthingsTokenInRequestParameter', 'App\Http\Controllers\SmartThingsController@resendRequestWithHeader');

Route::prefix('homes')->middleware('throttle:localInstances')->group(function(){
	Route::middleware('auth:api')->get('/', 'App\Http\Controllers\HomesController@get');
	Route::post('/', 'App\Http\Controllers\HomesController@create');
	Route::middleware('auth:token')->group(function() {
	    Route::post('connectionInfo', 'App\Http\Controllers\HomesController@getConnectionInfo');
	    Route::post('unlink', 'App\Http\Controllers\HomesController@unlinkLocalInstance');
	});
});

Route::prefix('mosquitto')->middleware('throttle:mqttAuth')->group(function() {
    Route::post('auth', 'App\Http\Controllers\MosquittoController@authenticateHome');
    Route::post('acl', 'App\Http\Controllers\MosquittoController@checkTopicPermission');
});