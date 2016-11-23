<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		echo "Seeding database\n";

		if (env('APP_DEBUG'))
		{
			$this->call(DebugSeeder::class);
		}
		else
		{
			$this->call(CategorySeeder::class);
		}
		
		$this->call(UserSeeder::class);
		$this->call(BlacklistSeeder::class);
	}
}
