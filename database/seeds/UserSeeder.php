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
		// Admin
		// This is only to get things up and running
		$user = App\Models\User::create([
			'username' => 'admin',
			'email' => 'webmaster@'.$_SERVER['SERVER_NAME'],
			'password' => bcrypt('password'),
		]);
	}
}
