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
			['name' => 'Bots', 'groups' => ['Ai', 'Bot'], 'type' => 'addon', 'icon' => 'category_bots.png'],
			['name' => 'Bricks', 'group' => 'Brick', 'type' => 'addon', 'icon' => 'category_bricks.png'],
			['name' => 'Clients', 'group' => 'Client', 'type' => 'addon', 'icon' => 'category_clients.png'],
			['name' => 'Client Scripts', 'group' => 'Client', 'type' => 'addon', 'icon' => 'category_client_scripts.png'],
			['name' => 'Colorsets', 'group' => 'Colorset', 'type' => 'addon', 'icon' => 'category_colorsets.png'],
			['name' => 'Day Cycles', 'group' => 'DayCycle', 'type' => 'addon', 'icon' => 'category_day_cycles.png'],
			['name' => 'Decals', 'group' => 'Decal', 'type' => 'addon', 'icon' => 'category_decals.png'],
			['name' => 'Emotes', 'group' => 'Emote', 'type' => 'addon', 'icon' => 'category_emotes.png'],
			['name' => 'Environments', 'group' => 'Environment', 'type' => 'addon', 'icon' => 'category_environments.png'],
			['name' => 'Events', 'group' => 'Event', 'type' => 'addon', 'icon' => 'category_events.png'],
			['name' => 'Faces', 'group' => 'Face', 'type' => 'addon', 'icon' => 'category_faces.png'],
			['name' => 'Gamemodes', 'group' => 'Gamemode', 'type' => 'addon', 'icon' => 'category_gamemodes.png'],
			['name' => 'Gamemode Maps', 'type' => 'save', 'icon' => 'category_saves_gamemode.png'],
			['name' => 'Grounds', 'group' => 'Ground', 'type' => 'addon', 'icon' => 'category_grounds.png'],
			['name' => 'Items', 'group' => 'Item', 'type' => 'addon', 'icon' => 'category_items.png'],
			['name' => 'Lights', 'group' => 'Light', 'type' => 'addon', 'icon' => 'category_lights.png'],
			['name' => 'Particles', 'group' => 'Particles', 'type' => 'addon', 'icon' => 'category_particles.png'],
			['name' => 'Players', 'group' => 'Player', 'type' => 'addon', 'icon' => 'category_players.png'],
			['name' => 'Prints', 'group' => 'Print', 'type' => 'addon', 'icon' => 'category_prints.png'],
			['name' => 'Projectiles', 'group' => 'Projectile', 'type' => 'addon', 'icon' => 'category_projectiles.png'],
			['name' => 'Saves', 'type' => 'save', 'icon' => 'category_saves.png'],
			['name' => 'Server Scripts', 'group' => 'Server', 'type' => 'addon', 'icon' => 'category_server_scripts.png'],
			['name' => 'Skies', 'group' => 'Sky', 'type' => 'addon', 'icon' => 'category_skies.png'],
			['name' => 'Sounds', 'group' => 'Sound', 'type' => 'addon', 'icon' => 'category_sounds.png'],
			['name' => 'SpeedKart Maps', 'group' => 'SpeedKart', 'type' => 'addon', 'icon' => 'category_saves_speedkart.png'],
			['name' => 'Support Scripts', 'group' => 'Support', 'type' => 'addon', 'icon' => 'category_support_scripts.png'],
			['name' => 'Systems', 'group' => 'System ', 'type' => 'addon', 'icon' => 'category_systems.png'],
			['name' => 'Tools', 'group' => 'Tool', 'type' => 'addon', 'icon' => 'category_tools.png'],
			['name' => 'Vehicles', 'group' => 'Vehicle', 'type' => 'addon', 'icon' => 'category_vehicles.png'],
			['name' => 'Waters', 'group' => 'Water', 'type' => 'addon', 'icon' => 'category_waters.png'],
			['name' => 'Weapons', 'group' => 'Weapon', 'type' => 'addon', 'icon' => 'category_weapons.png'],
		];

		// Go through the cateogires to be created
		foreach ($categories as $category)
		{
			// Get groups
			$groups = [];
			if (array_key_exists('group', $category))
			{
				$groups[] = $category['group'];
				unset($category['group']);
			}
			if (array_key_exists('groups', $category))
			{
				$groups = array_merge($groups, $category['groups']);
				unset($category['groups']);
			}

			// Make search faster
			array_map('strtolower', $groups);

			if (array_key_exists('type', $category))
			{
				$type = strtolower($category['type']);
				unset($category['type']);
				$type = App\Models\RepositoryType::firstOrCreate(['name' => $type]);
			}

			// Get/Create database groups
			$group_objs = [];
			foreach ($groups as $group)
				$group_objs[] = App\Models\AddonGroup::firstOrNew(['name' => $group]);

			$cat = new App\Models\Category($category);

			if (isset($type))
				//$cat->type()->save($type);
				$type->categories()->save($cat);
			// Add dependencies
			$cat->groups()->saveMany($group_objs);
		}
	}
}
