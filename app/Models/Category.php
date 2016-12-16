<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Attributes that are hidden.
	 *
	 * @var string
	 */
	protected $hidden = ['id'];

	/**
	 * The Attributes that are allowed to be Mass Assigned.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'icon', 'types'];

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Repositories that this Category has.
	 *
	 * @return Relationship
	 */
	public function repositories()
	{
		return $this->hasMany(Repository::class);
	}

	/////////////////
	//* Utilities *//
	/////////////////
	/**
	 * Gets all categories and puts them in a list used for selects.
	 *
	 * @return array
	 */
	public static function listSelect()
	{
		$cats = self::select('id', 'name')->get();
		return $cats->pluck('name', 'id');
	}

	/**
	 * Get id of category by type.
	 *
	 * @param string $type_name
	 * @return int
	 */
	public static function getByType($type_name)
	{
		$type_name = strtolower($type_name);
		$types = self::select('id')->where('types', 'like', "%{$type_name}%")->first();
		return $types ? $types->id : null;
	}
}
