<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Attributes that are hidden.
	 *
	 * @var string
	 */
	protected $hidden = ['id', 'addon_id'];

	/**
	 * The Attributes that are allowed to be Mass Assigned.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'slug', 'description'];

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
		static::deleting(function($channel)
		{
			$channel->versions()->delete();
		});

		// Model was created
		static::created(function($channel)
		{
			$version = new Version;
			$version->name = '0';
			$version->channel_id = $channel->id;
			$version->default = true;
			$channel->versions()->save($version);
		});
	}

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Addon this Channel belongs to.
	 *
	 * @return App\Model\Addon
	 */
	public function addon()
	{
		return $this->belongsTo(Addon::class);
	}

	/**
	 * Returns the Versions that this Addon has.
	 *
	 * @return Relationship
	 */
	public function versions()
	{
		return $this->hasMany(Version::class);
	}

	/**
	 * Returns the default Version that this Channel has.
	 *
	 * @return App\Model\Version
	 */
	public function getVersionAttribute()
	{
		return $this->versions()->default()->first();
	}

	/**
	 * Returns the Users that may access this Channel.
	 *
	 * @return Relationship
	 */
	public function users()
	{
		return $this->belongsToMany(User::class)->withTimestamps();
	}

	//////////////
	//* Scopes *//
	//////////////
	/**
	 * Returns the default Channels.
	 *
	 * @return Builder
	 */
	public function scopeDefault($query)
	{
		return $query->where('default', true);
	}

	// Note: Under development
	public function restart_required()
	{
		return null;
	}
}
