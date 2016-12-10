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

$addonFaker = Faker\Factory::create();
$addonFaker->addProvider(new App\Repository\AddonFakerFacade($addonFaker));

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker)
{
	static $password;
	$updated_at = $faker->unixTime();
	$created_at = $faker->unixTime($updated_at);

	return [
		'username' => $faker->userName,
		'email' => $faker->unique()->safeEmail,
		'password' => $password ?: $password = bcrypt('secret'),
		'remember_token' => str_random(10),
		'created_at' => $created_at,
		'updated_at' => $updated_at,
	];
});

$factory->define(App\Models\Category::class, function (Faker\Generator $faker)
{
	return [
		'name' => $faker->name,
		'icon' => 'category_unknown.png'
	];
});

$factory->define(App\Models\Addon::class, function (Faker\Generator $faker)
{
	$updated_at = $faker->unixTime();
	$created_at = $faker->unixTime($updated_at);
	$name = $faker->name;
	return [
		'name' => $name,
		'slug' => str_slug($name, '_'),
		'description' => $faker->text(512),
		'created_at' => $created_at,
		'updated_at' => $updated_at,
	];
});

$factory->define(App\Models\Channel::class, function (Faker\Generator $faker)
{
	$updated_at = $faker->unixTime();
	$created_at = $faker->unixTime($updated_at);
	return [
		'name' => $faker->randomElement(['debug', 'test']),
		'slug' => str_random(10),
		'description' => $faker->text(512),
		'created_at' => $created_at,
		'updated_at' => $updated_at,
	];
});

$factory->define(App\Models\Version::class, function (Faker\Generator $faker)
	use(&$addonFaker)
{
	$updated_at = $faker->unixTime();
	$created_at = $faker->unixTime($updated_at);
	return [
		'name' => $addonFaker->sem_ver,
		'change_log' => $faker->text(128),
		'created_at' => $created_at,
		'updated_at' => $updated_at,
	];
});

$factory->define(App\Models\File::class, function (Faker\Generator $faker)
	use(&$addonFaker)
{
	$updated_at = $faker->unixTime();
	$created_at = $faker->unixTime($updated_at);
	return [
		'display_name' => $addonFaker->addon_name,
		'path' => $addonFaker->md5.'.zip',
		'size' => $addonFaker->numberBetween(1000, 50000000),
		'extension' => 'zip',
		'mime' => 'application/zip',
		'created_at' => $created_at,
		'updated_at' => $updated_at,
	];
});
