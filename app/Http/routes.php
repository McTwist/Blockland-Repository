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
Route::group(['middleware' => []], function() {

	// The Home Page
	Route::get('/', array(
		'uses' 	=> 'PagesController@home',
		'as' 	=> 'pages.home'
	));

	/*
	|--------------------------------------------------------------------------
	| Category Routes
	|--------------------------------------------------------------------------
	|
	| All Routes pertaining to Categories are defined here. These routes are
	| prefixed by the 'categories' noun, and use the '{category}' wildcard
	| when necessary. The Route Names are prefixed with 'categories'.
	|
	*/
	Route::resource('categories', 'CategoriesController', [
		'parameters' => 'singular'
	]);

	/*
	|--------------------------------------------------------------------------
	| Addon Routes
	|--------------------------------------------------------------------------
	|
	| All Routes pertaining to Addons are defined here. These routes are
	| prefixed by the 'addon' noun, and use the '{addon}' wildcard
	| when necessary. The Route Names are prefixed with 'addon'.
	|
	*/
	Route::put('/addon/upload', array(
		'uses' => 'AddonController@upload',
		'as' => 'addon.upload'
	));
	Route::resource('addon', 'AddonController', [
		'except' => 'index'
	]);

	/*
	|--------------------------------------------------------------------------
	| User Routes
	|--------------------------------------------------------------------------
	|
	| All Routes pertaining to Users are defined here. These routes are
	| prefixed by the 'user' noun, and use the '{user}' wildcard
	| when necessary. The Route Names are prefixed with 'user'.
	|
	*/
	Route::get('/user/login', array(
		'uses' => 'Auth\AuthController@getLogin',
		'as' => 'user.login'
	));
	Route::post('/user/login', array(
		'uses' => 'Auth\AuthController@postLogin',
		'as' => 'user.login'
	));
	Route::get('/user/logout', array(
		'uses' => 'Auth\AuthController@logout',
		'as' => 'user.logout',
		'middleware' => 'auth'
	));

	Route::get('/user/register', array(
		'uses' => 'Auth\AuthController@getRegister',
		'as' => 'user.register'
	));
	Route::post('/user/register', array(
		'uses' => 'Auth\AuthController@postRegister',
		'as' => 'user.register'
	));

	Route::get('/user/show/{user?}', array(
		'uses' => 'UserController@show',
		'as' => 'user.show'
	));
	Route::get('/user/edit', array(
		'uses' => 'UserController@edit',
		'as' => 'user.edit',
		'middleware' => 'auth'
	));
	Route::put('/user/update', array(
		'uses' => 'UserController@update',
		'as' => 'user.update',
		'middleware' => 'auth'
	));

	Route::get('/auth/ip', array(
		'uses' => 'UserController@validateAuthIp',
		'as' => 'auth.ip',
		'middleware' => ['api', 'auth']
	));

	/*
	|--------------------------------------------------------------------------
	| Password Routes
	|--------------------------------------------------------------------------
	|
	| All Routes pertaining to Passwords are defined here. These routes are
	| prefixed by the 'password' noun, and use the '{token}' wildcard
	| when necessary. The Route Names are prefixed with 'password'.
	|
	*/
	Route::get('/password/email', array(
		'uses' => 'Auth\PasswordController@getEmail',
		'as' => 'password.email'
	));
	Route::post('/password/email', array(
		'uses' => 'Auth\PasswordController@postEmail',
		'as' => 'password.email'
	));
	Route::get('/password/reset/{token}', array(
		'uses' => 'Auth\PasswordController@getReset',
		'as' => 'password.reset'
	));
	Route::post('/password/reset', array(
		'uses' => 'Auth\PasswordController@postReset',
		'as' => 'password.reset'
	));

});

Route::group(['middleware' => ['api']], function()
{
	Route::get('/api', 'RepositoryApiController@home');
	Route::get('/api/mods/{mods}', 'RepositoryApiController@mods');
	Route::get('/api/mod/{mod}', 'RepositoryApiController@mod');
	Route::get('/api/mod/{mod}.zip', 'RepositoryApiController@download');
	Route::get('/api/repo/{repo}', 'RepositoryApiController@repository');
	// Catch-all
	Route::get('/api/{null}', 'RepositoryApiController@home');
});
