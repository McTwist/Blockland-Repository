<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The group associated with an Add-On.
 * This can be anything from client, speedkart, face, etc.
 */
class AddonGroup extends Model
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Attributes that are hidden.
	 *
	 * @var string
	 */
	protected $hidden = ['id', 'category_id'];

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
	 * Returns the Category that this AddonGroup belongs to.
	 *
	 * @return Relationship
	 */
	public function category()
	{
		return $this->belongsTo(Category::class);
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
