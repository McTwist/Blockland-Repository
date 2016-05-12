<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['api']], function()
{
	Route::get('/api', 'RepositoryApiController@home');
	Route::get('/api/mods/{mods}', 'RepositoryApiController@mods');
	Route::get('/api/mod/{mod}', 'RepositoryApiController@mod');
	Route::get('/api/repo/{repo}', 'RepositoryApiController@repository');
	// Catch-all
	Route::get('/api/{null}', 'RepositoryApiController@home');
});
