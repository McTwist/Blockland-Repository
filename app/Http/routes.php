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

Route::get('/category/{category}', 'ViewController@category');
Route::get('/addon/{addon}', 'ViewController@addon');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All Routes that require some sort of Session information belong here.
| The majority of the site content will be housed here. Typically all
| APIs and Stateless Mechanisms should not be placed in this group.
|
*/
Route::group(['middleware' => ['web']], function() {

	// The Home Page
	Route::get('/', array(
		'uses' 	=> 'PagesController@home',
		'as' 	=> 'pages.home'
	));

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
