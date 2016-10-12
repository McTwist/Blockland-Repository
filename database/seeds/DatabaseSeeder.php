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
		
		$max_categories = 10;
		$max_users = 10;
		$max_addons_per_user = 5;
		$max_channels_per_addon = 3;
		$max_versions_per_channel = 2;

		// Create categories
		$categories = factory(App\Models\Category::class, $max_categories)->create();

		// Create users
		$users = factory(App\Models\User::class, $max_users)->create()->each(function($u)
			use ($max_addons_per_user, $max_channels_per_addon, $max_versions_per_channel, $max_categories, &$categories)
		{
			// Create addons
			$addons = factory(App\Models\Addon::class, mt_rand(2, $max_addons_per_user))->create()->each(function($a)
				use ($max_channels_per_addon, $max_versions_per_channel, $max_categories, &$categories)
			{
				// Attach category to addon
				$categories->random()->addons()->save($a);

				// Create channels
				$channels = factory(App\Models\Channel::class, mt_rand(2, $max_channels_per_addon))->create()->each(function($c)
					use ($max_versions_per_channel)
				{
					// Create versions
					$versions = factory(App\Models\Version::class, mt_rand(2, $max_versions_per_channel))->create();

					$versions->first()->default = 1;

					// Handle singles and none
					if (count($versions) > 1)
						$c->versions()->saveMany($versions);
					elseif (count($versions) > 0)
						$c->versions()->save($versions);
				});

				$channels->first()->default = 1;

				// Handle singles and none
				if (count($channels) > 1)
					$a->channels()->saveMany($channels);
				elseif (count($channels) > 0)
					$a->channels()->save($channels);
			});

			// Handle singles and none
			if (count($addons) > 1)
				$u->addons()->saveMany($addons);
			elseif (count($addons) > 0)
				$u->addons()->save($addons);
		});
	}
}
