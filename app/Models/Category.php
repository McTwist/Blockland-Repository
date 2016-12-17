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
	protected $hidden = ['id', 'repository_type_id'];

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

	/**
	 * Returns the AddonGroup that this Category has.
	 *
	 * @return Relationship
	 */
	public function groups()
	{
		return $this->hasMany(AddonGroup::class);
	}

	/**
	 * Returns the RepositoryType that this Category belongs to.
	 *
	 * @return Relationship
	 */
	public function type()
	{
		return $this->belongsTo(RepositoryType::class, 'repository_type_id');
	}

	/////////////////
	//* Utilities *//
	/////////////////
	/**
	 * Gets all categories and puts them in a list used for selects.
	 *
	 * @param string $type
	 * @return array
	 */
	public static function listSelect($type = null)
	{
		$query = self::select('id', 'name');
		if ($type)
			$query->whereHas('type', function($query) use ($type)
			{
				$query->where('name', $type);
			});
		$cats = $query->get();
		return $cats->pluck('name', 'id');
	}

	/**
	 * Get id of category by AddonGroup.
	 *
	 * @param string $group
	 * @return int
	 */
	public static function getIdByAddonGroup($group)
	{
		$group = strtolower($group);
		$types = self::select('id')->whereHas('groups', function($query) use ($group)
		{
			$query->where('name', $group);
		})->first();
		return $types ? $types->id : null;
	}
}
