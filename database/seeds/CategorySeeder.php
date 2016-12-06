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
			['name' => 'Bots', 'types' => ['Ai', 'Bot'], 'icon' => 'category_bots.png'],
			['name' => 'Bricks', 'type' => 'Brick', 'icon' => 'category_bricks.png'],
			['name' => 'Clients', 'type' => 'Client', 'icon' => 'category_clients.png'],
			['name' => 'Client Scripts', 'type' => 'Client', 'icon' => 'category_client_scripts.png'],
			['name' => 'Colorsets', 'type' => 'Colorset', 'icon' => 'category_colorsets.png'],
			['name' => 'Day Cycles', 'type' => 'DayCycle', 'icon' => 'category_day_cycles.png'],
			['name' => 'Decals', 'type' => 'Decal', 'icon' => 'category_decals.png'],
			['name' => 'Emotes', 'type' => 'Emote', 'icon' => 'category_emotes.png'],
			['name' => 'Environments', 'type' => 'Environment', 'icon' => 'category_environments.png'],
			['name' => 'Events', 'type' => 'Event', 'icon' => 'category_events.png'],
			['name' => 'Faces', 'type' => 'Face', 'icon' => 'category_faces.png'],
			['name' => 'Gamemodes', 'type' => 'Gamemode', 'icon' => 'category_gamemodes.png'],
			['name' => 'Grounds', 'type' => 'Ground', 'icon' => 'category_grounds.png'],
			['name' => 'Items', 'type' => 'Item', 'icon' => 'category_items.png'],
			['name' => 'Lights', 'type' => 'Light', 'icon' => 'category_lights.png'],
			['name' => 'Particles', 'type' => 'Particles', 'icon' => 'category_particles.png'],
			['name' => 'Players', 'type' => 'Player', 'icon' => 'category_players.png'],
			['name' => 'Prints', 'type' => 'Print', 'icon' => 'category_prints.png'],
			['name' => 'Projectiles', 'type' => 'Projectile', 'icon' => 'category_projectiles.png'],
			['name' => 'Server Scripts', 'type' => 'Server', 'icon' => 'category_server_scripts.png'],
			['name' => 'Skies', 'type' => 'Sky', 'icon' => 'category_skies.png'],
			['name' => 'Sounds', 'type' => 'Sound', 'icon' => 'category_sounds.png'],
			['name' => 'SpeedKart Maps', 'type' => 'SpeedKart', 'icon' => 'category_saves_speedkart.png'],
			['name' => 'Support Scripts', 'type' => 'Support', 'icon' => 'category_support_scripts.png'],
			['name' => 'Systems', 'type' => 'System ', 'icon' => 'category_systems.png'],
			['name' => 'Tools', 'type' => 'Tool', 'icon' => 'category_tools.png'],
			['name' => 'Vehicles', 'type' => 'Vehicle', 'icon' => 'category_vehicles.png'],
			['name' => 'Waters', 'type' => 'Water', 'icon' => 'category_waters.png'],
			['name' => 'Weapons', 'type' => 'Weapon', 'icon' => 'category_weapons.png'],
		];

		$insert = [];

		// Go through the cateogires to be created
		foreach ($categories as $category)
		{
			// Get types
			$types = [];
			if (array_key_exists('type', $category))
			{
				$types[] = $category['type'];
				unset($category['type']);
			}
			if (array_key_exists('types', $category))
				$types = array_merge($types, $category['types']);

			// Make search faster
			array_map('strtolower', $types);
			// Put together to store away
			// Note: Using slash as that cannot be used in a path
			$category['types'] = implode('/', $types);

			$insert[] = $category;
		}

		App\Models\Category::insert($insert);
	}
}
