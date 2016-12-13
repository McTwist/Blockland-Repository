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
			foreach ($channel->versions as $version)
				$version->delete();
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
	 * Returns the Versions that this Channel has.
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
	 * @return Relationship
	 */
	public function version()
	{
		return $this->hasOne(Version::class)->where("default", true);
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

	///////////////////////////
	//* Attribute Overrides *//
	///////////////////////////
	/**
	 * Set the default attribute.
	 * You can only set this to true.
	 *
	 * @return void
	 */
	public function setDefaultAttribute($bool)
	{
		if ($bool)
		{
			$this->makeDefault();
		}
	}

	/**
	 * Get which Version requires the client to restart.
	 * Note: Under development
	 *
	 * @return unknown|null
	 */
	public function getRestartRequiredAttribute()
	{
		return null;
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

	/////////////////
	//* Utilities *//
	/////////////////
	/**
	 * Makes the Channel default
	 *
	 * @return void
	 */
	public function makeDefault()
	{
		if ($this->default)
			return;

		// Remove default from old Channel
		$channel = $this->addon->channel;
		if ($channel)
		{
			$channel->attributes['default'] = false;
			$channel->save();
		}

		// Add default to this Channel
		$this->attributes['default'] = true;
		$this->save();
	}
}
