<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// reverse-proxy support
$proxy_url    = env('PROXY_URL');
$proxy_schema = env('PROXY_SCHEMA');
if (!empty($proxy_url)) URL::forceRootUrl($proxy_url);
if (!empty($proxy_schema)) URL::forceScheme($proxy_schema);


Route::get('/', function () {
    return 'swipeback interface';
});

