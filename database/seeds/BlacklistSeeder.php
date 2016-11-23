<?php

use Illuminate\Database\Seeder;

class BlacklistSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$blacklist = new App\Models\AddonBlacklist;
		$blacklist->parseScript(database_path('data/config.cs'));
	}
}
