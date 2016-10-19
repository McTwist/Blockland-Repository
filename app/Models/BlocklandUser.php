<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlocklandUser extends Model
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Attributes that are allowed to be Mass Assigned.
	 *
	 * @var array
	 */
	protected $fillable = ['id', 'name'];

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the User that this BlocklandUser belongs to.
	 *
	 * @return Relationship
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'blockland_id');
	}
}
