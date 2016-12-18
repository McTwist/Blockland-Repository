<?php

use Illuminate\Database\Seeder;

class DebugSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$max_users = 10;
		$max_addons_per_user = 10;
		$max_channels_per_addon = 3;
		$max_versions_per_channel = 2;

		// Get addon type
		$type = App\Models\RepositoryType::where('name', 'addon')->first();
		// Get categories
		$categories = App\Models\Category::where('repository_type_id', $type->id)->get();

		// Note: Due to some odd internal functionality, enormous amounts of files were created
		// with previous algorithm. Due to that, this was made instead. Hopefully it'll work better.

		// Create users
		$users = factory(App\Models\User::class, $max_users)->create();

		// Create addons
		$addons = factory(App\Models\Repository::class, mt_rand(2, $max_addons_per_user * count($users)))->create()->each(function($a)
			use(&$users, &$categories, &$type)
		{
			// Attach category to addon
			$categories->random()->repositories()->save($a);

			$users->random()->repositories()->save($a);
			if ($type)
				$type->repositories()->save($a);
		});

		// Create channels
		factory(App\Models\Channel::class, mt_rand(0, ($max_channels_per_addon - 1) * count($addons)))->make()->each(function($c)
			use(&$addons)
		{
			$addons->random()->channels()->save($c);
		});

		// Get all channels created
		$channels = App\Models\Channel::all();

		// Create versions
		factory(App\Models\Version::class, mt_rand(0, ($max_versions_per_channel - 1) * count($channels)))->make()->each(function($v)
			use(&$channels)
		{
			$channels->random()->versions()->save($v);
		});

		// Get all versions created
		$versions = App\Models\Version::all();

		// Create files for versions
		// Note: These cannot be downloaded
		foreach ($versions as $v)
		{
			// Make file
			$file = factory(App\Models\File::class)->make();
			$file->uploader()->associate($v->repository->owners->first());
			// ... save file
			$v->file()->save($file);
		}
	}
}
