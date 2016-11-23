<?php

use Illuminate\Http\Request;

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

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');*/

Route::get('/', 'RepositoryApiController@home');
Route::get('/mods/{mods}', 'RepositoryApiController@mods');
Route::get('/mod/{mod}.zip', 'RepositoryApiController@download');
Route::get('/mod/{mod}', 'RepositoryApiController@mod');
Route::get('/repo/{repo}', 'RepositoryApiController@repository');
// Catch-all
Route::get('/{null}', 'RepositoryApiController@home');
