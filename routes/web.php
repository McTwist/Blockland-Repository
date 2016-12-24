<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

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

// Get tags depending on one tag
Route::get('/tags/{tag?}', array(
	'uses' => 'CategoriesController@tags',
	'as' => 'categories.tags'
));

/*
|--------------------------------------------------------------------------
| Popup Routes
|--------------------------------------------------------------------------
|
| Routes to popup dialogs.
| If accessed with AJAX calls returns views contained in a single div.
| If accessed directly from the browser, a different standalone view can be delivered if it exist.
|
*/
Route::get('/file/upload', array(
	'uses' => 'PopupController@getUploadFileView',
	'as' => 'file.upload'
));

Route::get('/user/login', array(
	'uses' => 'PopupController@getLoginView',
	'as' => 'user.login'
));

/*
|--------------------------------------------------------------------------
| File Routes
|--------------------------------------------------------------------------
|
| All Routes pertaining to files are defined here. These routes are
| prefixed by the 'file' noun, and use the '{file}' wildcard
| when necessary. The Route Names are prefixed with 'file'.
|
*/
Route::put('/file/upload', array(
	'uses' => 'UploadController@upload',
	'as' => 'file.upload'
));

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
Route::resource('addon', 'AddonController', [
	'except' => 'index'
]);

/*
|--------------------------------------------------------------------------
| BLSave Routes
|--------------------------------------------------------------------------
|
| All Routes pertaining to Saves are defined here. These routes are
| prefixed by the 'save' noun, and use the '{save}' wildcard
| when necessary. The Route Names are prefixed with 'save'.
|
*/
Route::resource('save', 'BlocklandSaveController', [
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
Route::post('/user/login', array(
	'uses' => 'Auth\LoginController@login',
	'as' => 'user.login'
));
Route::get('/user/logout', array(
	'uses' => 'Auth\LoginController@logout',
	'as' => 'user.logout',
	'middleware' => 'auth'
));

Route::get('/user/register', array(
	'uses' => 'Auth\RegisterController@showRegistrationForm',
	'as' => 'user.register'
));
Route::post('/user/register', array(
	'uses' => 'Auth\RegisterController@register',
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
	'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm',
	'as' => 'password.email'
));
Route::post('/password/email', array(
	'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail',
	'as' => 'password.email'
));
Route::get('/password/reset/{token}', array(
	'uses' => 'Auth\ResetPasswordController@showResetForm',
	'as' => 'password.reset'
));
Route::post('/password/reset', array(
	'uses' => 'Auth\ResetPasswordController@reset',
	'as' => 'password.reset'
));
