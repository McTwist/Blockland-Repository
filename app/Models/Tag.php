<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
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

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Repositories that this Tag has.
	 *
	 * @return Relationship
	 */
	public function repositories()
	{
		return $this->belongsToMany(Repository::class)->withTimestamps();
	}
}
