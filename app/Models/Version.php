<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Attributes that are hidden.
	 *
	 * @var string
	 */
	protected $hidden = ['id', 'channel_id'];

	/**
	 * The Attributes that are allowed to be Mass Assigned.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'change_log'];

	/**
	 * The Attributes that are casts to a specific type.
	 *
	 * @var array
	 */
	protected $casts = [
		'default' => 'boolean'
	];

	/////////////////////
	//* Boot Override *//
	/////////////////////
	/**
	 * This method handles any triggers associated with this Model.
	 *
	 * @return void
	 */
	public static function boot()
	{
		parent::boot();
		
		// Deleting the model
		static::deleting(function($version)
		{
			$version->file->delete();
		});
	}

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Channel that this Version belongs to.
	 *
	 * @return Relationship
	 */
	public function channel()
	{
		return $this->belongsTo(Channel::class);
	}

	/**
	 * Returns the Addon that this Version belongs to through Channel.
	 *
	 * @return Relationship
	 */
	public function addon()
	{
		// TODO: Try to nestle some lazy loading in here
		return $this->channel->addon();
	}

	/**
	 * Returns the File that this Version has.
	 *
	 * @return Relationship
	 */
	public function file()
	{
		return $this->morphOne(File::class, 'link');
	}

	//////////////
	//* Scopes *//
	//////////////
	/**
	 * Returns the default Versions.
	 *
	 * @return Builder
	 */
	public function scopeDefault($query)
	{
		return $query->where('default', true);
	}
}
