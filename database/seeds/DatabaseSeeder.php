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

		$this->call(CategorySeeder::class);

		if (env('APP_DEBUG'))
		{
			$this->call(DebugSeeder::class);
		}
		
		$this->call(UserSeeder::class);
		$this->call(BlacklistSeeder::class);
	}
}
