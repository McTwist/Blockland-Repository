<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

$autoloadPath = __DIR__.'/../vendor/autoload.php';

if(file_exists($autoloadPath)) {
	require $autoloadPath;
}
else {
	require __DIR__ . '/composer.php';
	die();
}

/*
|--------------------------------------------------------------------------
| Include The Compiled Class File
|--------------------------------------------------------------------------
|
| To dramatically increase your application's performance, you may use a
| compiled class file which contains all of the classes commonly used
| by a request. The Artisan "optimize" is used to create this file.
|
*/

$compiledPath = __DIR__.'/cache/compiled.php';

if(file_exists($compiledPath)) {
    require $compiledPath;
}

/*
|--------------------------------------------------------------------------
| Make sure an Environment File Exists
|--------------------------------------------------------------------------
|
| The .env Environment File is used to make this instance of the project
| unique with respect to the developer's custom environment. If such a
| file does not exist, the several core features simply won't work.
|
*/

$envPath =  __DIR__.'/../.env';

if(!file_exists($envPath)) {
	require __DIR__ . '/environment.php';
	die();
}