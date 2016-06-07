<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	protected $hidden = ['id'];

	protected $fillable = ['name', 'icon'];

	public function addons()
	{
		return $this->hasMany(Addon::class);
	}

	// Get list of categories used for selects
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
}
