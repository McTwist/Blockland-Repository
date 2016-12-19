<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$server_name = array_key_exists('SERVER_NAME', $_SERVER) ? $_SERVER['SERVER_NAME'] : php_uname('n');
		// Admin
		// This is only to get things up and running
		$user = App\Models\User::create([
			'username' => 'admin',
			'displayname' => 'Admin',
			'email' => 'webmaster@'.$server_name,
			'password' => bcrypt('password'),
		]);
	}
}
