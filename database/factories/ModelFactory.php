<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker)
{
	static $password;

	return [
		'username' => $faker->userName,
		'email' => $faker->unique()->safeEmail,
		'password' => $password ?: $password = bcrypt('secret'),
		'remember_token' => str_random(10),
	];
});

$factory->define(App\Models\Category::class, function (Faker\Generator $faker)
{
	return [
		'name' => $faker->name,
		'icon' => 'none'
	];
});

$factory->define(App\Models\Addon::class, function (Faker\Generator $faker)
{
	$name = $faker->name;
	return [
		'name' => $name,
		'slug' => str_slug($name, '_'),
		'description' => $faker->text(512),
	];
});

$factory->define(App\Models\Channel::class, function (Faker\Generator $faker)
{
	return [
		'name' => $faker->randomElements(['debug', 'test'])[0],
		'slug' => str_random(10),
		'description' => $faker->text(512),
	];
});

$factory->define(App\Models\Version::class, function (Faker\Generator $faker)
{
	return [
		'name' => '0',
		'change_log' => $faker->text(128),
	];
});

$factory->define(App\Models\File::class, function (Faker\Generator $faker)
{
	return [
		'display_name' => $faker->slug(10),
		'path' => $faker->md5.'.zip',
		'size' => $faker->numberBetween(1000, 50000000),
		'extension' => 'zip',
		'mime' => 'application/zip',
	];
});
