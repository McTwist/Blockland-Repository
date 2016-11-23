<?php

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// Categories with preferences
		$categories = [
			['name' => 'Bots', 'tags' => ['Ai', 'Bot'], 'icon' => 'category_bots.png'],
			['name' => 'Bricks', 'tag' => 'Brick', 'icon' => 'category_bricks.png'],
			['name' => 'Clients', 'tag' => 'Client', 'icon' => 'category_clients.png'],
			['name' => 'Client Scripts', 'tag' => 'Client', 'icon' => 'category_client_scripts.png'],
			['name' => 'Colorsets', 'tag' => 'Colorset', 'icon' => 'category_colorsets.png'],
			['name' => 'Day Cycles', 'tag' => 'DayCycle', 'icon' => 'category_day_cycles.png'],
			['name' => 'Decals', 'tag' => 'Decal', 'icon' => 'category_decals.png'],
			['name' => 'Emotes', 'tag' => 'Emote', 'icon' => 'category_emotes.png'],
			['name' => 'Environments', 'tag' => 'Environment', 'icon' => 'category_environments.png'],
			['name' => 'Events', 'tag' => 'Event', 'icon' => 'category_events.png'],
			['name' => 'Faces', 'tag' => 'Face', 'icon' => 'category_faces.png'],
			['name' => 'Gamemodes', 'tag' => 'Gamemode', 'icon' => 'category_gamemodes.png'],
			['name' => 'Grounds', 'tag' => 'Ground', 'icon' => 'category_grounds.png'],
			['name' => 'Items', 'tag' => 'Item', 'icon' => 'category_items.png'],
			['name' => 'Lights', 'tag' => 'Light', 'icon' => 'category_lights.png'],
			['name' => 'Particles', 'tag' => 'Particles', 'icon' => 'category_particles.png'],
			['name' => 'Players', 'tag' => 'Player', 'icon' => 'category_players.png'],
			['name' => 'Prints', 'tag' => 'Print', 'icon' => 'category_prints.png'],
			['name' => 'Projectiles', 'tag' => 'Projectile', 'icon' => 'category_projectiles.png'],
			['name' => 'Server Scripts', 'tag' => 'Server', 'icon' => 'category_server_scripts.png'],
			['name' => 'Skies', 'tag' => 'Sky', 'icon' => 'category_skies.png'],
			['name' => 'Sounds', 'tag' => 'Sound', 'icon' => 'category_sounds.png'],
			['name' => 'SpeedKart Maps', 'tag' => 'SpeedKart', 'icon' => 'category_speedkart_maps.png'],
			['name' => 'Support Scripts', 'tag' => 'Support', 'icon' => 'category_support_scripts.png'],
			['name' => 'Systems', 'tag' => 'System ', 'icon' => 'category_systems.png'],
			['name' => 'Tools', 'tag' => 'Tool', 'icon' => 'category_tools.png'],
			['name' => 'Vehicles', 'tag' => 'Vehicle', 'icon' => 'category_vehicles.png'],
			['name' => 'Waters', 'tag' => 'Water', 'icon' => 'category_waters.png'],
			['name' => 'Weapons', 'tag' => 'Weapon', 'icon' => 'category_weapons.png'],
		];

		$insert = [];

		// Go through the cateogires to be created
		foreach ($categories as $category)
		{
			// Get tags
			$tags = [];
			if (array_key_exists('tag', $category))
			{
				$tags[] = $category['tag'];
				unset($category['tag']);
			}
			if (array_key_exists('tags', $category))
				$tags = array_merge($tags, $category['tags']);

			// Make search faster
			array_map('strtolower', $tags);
			// Put together to store away
			// Note: Using slash as that cannot be used in a path
			$category['tags'] = implode('/', $tags);

			$insert[] = $category;
		}

		App\Models\Category::insert($insert);
	}
}
