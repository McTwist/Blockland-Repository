<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The type associated with a Repository.
 * This is to separate Repositories with each other and put them in special categories.
 */
class RepositoryType extends Model
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
	protected $fillable = ['name'];

	public $timestamps = false;

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Repositories that this RepositoryType has.
	 *
	 * @return Relationship
	 */
	public function repositories()
	{
		return $this->hasMany(Repository::class);
	}

	/**
	 * Returns the Categories that this RepositoryType has.
	 *
	 * @return Relationship
	 */
	public function categories()
	{
		return $this->hasMany(Category::class);
	}

	///////////////////////////
	//* Attribute Overrides *//
	///////////////////////////
	/**
	 * Set the name property to allways be lowercase.
	 *
	 * @return void
	 */
	public function setNameAttribute($value)
	{
		$this->attributes['name'] = strtolower($value);
	}
}
