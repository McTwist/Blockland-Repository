<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Attributes that are hidden.
	 *
	 * @var string
	 */
	protected $hidden = ['id', 'user_id'];

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
	 * Returns the Versions that this Author has.
	 *
	 * @return Relationship
	 */
	public function version()
	{
		return $this->belongsToMany(Version::class);
	}

	/**
	 * Returns the User that this Author belongs to.
	 *
	 * @return Relationship
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
