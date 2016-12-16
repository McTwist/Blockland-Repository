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
	protected $hidden = ['id', 'repository_id'];

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
			$channel->createVersion('0', true);
		});
	}

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Repository this Channel belongs to.
	 *
	 * @return Relationship
	 */
	public function repository()
	{
		return $this->belongsTo(Repository::class);
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
	 * Create a Version and return it.
	 *
	 * @param string $name The name of the Version.
	 * @param bool $default Set as default Version.
	 *
	 * @return Version
	 */
	public function createVersion($name, $default = false)
	{
		$version_obj = new Version;
		$version_obj->name = $name;
		$this->versions()->save($version_obj);
		if ($default)
			$version_obj->default = true;
		return $version_obj;
	}

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
		$channel = $this->repository->channel;
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
