<?php
namespace App\Repository;

use Faker\Provider\Base as FacadeBase;

class AddonFakerFacade extends FacadeBase
{
	protected static $typesList = [
		'Bot', 'Script', 'Brick', 'Client', 'Daycycle', 'Decal', 'Face', 'Emote', 'Sound',
		'Gamemode', 'Ground', 'Event', 'Item', 'Light', 'Particle', 'Player', 'Projectile',
		'Server', 'Tool', 'Vehicle', 'Weapon', 'Print', 'Sky', 'Water'
	];

	/**
	 * Generate a valid Add-On name.
	 *
	 * @return string
	 */
	public function addon_name()
	{
		$type = $this->generator->randomElement(static::$typesList);
		$name = studly_case($this->generator->words(mt_rand(1, 3), true));
		return "{$type}_{$name}";
	}
};
