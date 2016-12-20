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
			$version->cache->delete();
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
	 * Returns the Repository that this Version belongs to through Channel.
	 *
	 * @return Relationship
	 */
	public function repository()
	{
		// TODO: Try to nestle some lazy loading in here
		return $this->channel->repository();
	}

	/**
	 * Returns the Authors that this Version belongs to.
	 *
	 * @return Relationship
	 */
	public function authors()
	{
		return $this->belongsToMany(Author::class);
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

	/**
	 * Returns the VersionCache that this Version has.
	 *
	 * @return Relationship
	 */
	public function cache()
	{
		return $this->hasOne(VersionCache::class);
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

	/////////////////
	//* Utilities *//
	/////////////////
	/**
	 * Makes the Version default
	 *
	 * @return void
	 */
	public function makeDefault()
	{
		if ($this->default)
			return;

		// Remove default from old Version
		$version = $this->channel->version;
		if ($version)
		{
			$version->attributes['default'] = false;
			$version->save();
		}

		// Add default to this Version
		$this->attributes['default'] = true;
		$this->save();
	}
}
