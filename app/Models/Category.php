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
	protected $fillable = ['name', 'icon', 'tags'];

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Addons that this Category has.
	 *
	 * @return Relationship
	 */
	public function addons()
	{
		return $this->hasMany(Addon::class);
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
		$categories = [];
		foreach ($cats as $category)
		{
			$categories[(int)$category->id] = $category->name;
		}
		return $categories;
	}

	/**
	 * Get id of category by tag.
	 *
	 * @param string $tag_name
	 * @return int
	 */
	public static function getByTag($tag_name)
	{
		$tag_name = strtolower($tag_name);
		$tags = self::select('id')->where('tags', 'like', "%{$tag_name}%")->first();
		return $tags ? $tags->id : null;
	}
}
