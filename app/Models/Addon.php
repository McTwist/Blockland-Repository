<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
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
	protected $fillable = ['name', 'slug', 'description'];

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
		static::deleting(function($addon)
		{
			$addon->channels()->delete();
		});

		// Model was created
		static::created(function($addon)
		{
			$channel = new Channel;
			$channel->name = 'release';
			$channel->slug = $addon->slug.'_release';
			$channel->addon_id = $addon->id;
			$channel->default = true;
			$addon->channels()->save($channel);
		});
	}

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Channels that this Addon has.
	 *
	 * @return Relationship
	 */
	public function channels()
	{
		return $this->hasMany(Channel::class);
	}

	/**
	 * Returns the default Channel that this Addon has.
	 *
	 * @return App\Models\Channel
	 */
	public function getChannelAttribute()
	{
		return $this->channels()->default()->first();
	}

	/**
	 * Returns the Versions that this Addon has.
	 *
	 * @return Relationship
	 */
	public function versions()
	{
		return $this->hasManyThrough(Version::class, Channel::class);
	}

	/**
	 * Returns the default Version that this Addon has.
	 *
	 * @return App\Models\Version
	 */
	public function getVersionAttribute()
	{
		return $this->channel->version;
	}

	/**
	 * Returns the Users that this Addon belongs to.
	 *
	 * @return Relationship
	 */
	public function owners()
	{
		return $this->belongsToMany(User::class)->withTimestamps();
	}

	/**
	 * Returns the Category that this Addon belongs to.
	 *
	 * @return Relationship
	 */
	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	/////////////////
	//* Utilities *//
	/////////////////
	/**
	 * Returns the slug that is unique to this Addon.
	 *
	 * @return string
	 */
	public static function fromSlug($slug)
	{
		return self::where('slug', $slug)->first();
	}

	/**
	 * Returns true if User are owner to this Addon.
	 *
	 * @return boolean
	 */
	public function isOwner(User $user)
	{
		return $this->owners->contains($user);
	}

	/**
	 * Returns the Users that made this Addon.
	 *
	 * @return string
	 */
	public function authors()
	{
		// TODO: Authors is mentioned in add-on
		// Note: This might return a string, but find a better way to use it
		// Note2: Should come from cache table
		return $this->owners->implode('username', ', ');
	}

	///////////////////////////
	//* Attribute Overrides *//
	///////////////////////////
	// Note: All attributes should later on read from default version or defined slug
	/**
	 * Get the size of the Addon
	 *
	 * @return string
	 */
	public function getSizeAttribute()
	{
		return $this->version->file->size;
	}

	/**
	 * Get the size in metric of the Addon.
	 *
	 * @return string
	 */
	public function getSizeMetAttribute()
	{
		return static::formatBytesMet($this->size);
	}

	/**
	 * Get the size in bin of the Addon.
	 *
	 * @return string
	 */
	public function getSizeBinAttribute()
	{
		return static::formatBytesBin($this->size);
	}

	/**
	 * Get the summary of the Addon.
	 *
	 * @return string
	 */
	public function getSummaryAttribute()
	{
		// TODO: Read from cache table
		return 'SUMMARY NOT DONE';
	}

	/**
	 * Get the description to be displayed on site.
	 *
	 * @return string
	 */
	public function getDescriptionHtmlAttribute()
	{
		return nl2br(e($this->description));
	}

	/**
	 * Get the Version name of the Addon.
	 *
	 * @return string
	 */
	public function getVersionNameAttribute()
	{
		return $this->version->name;
	}

	/**
	 * Get amounts of downloads of the Addon.
	 *
	 * @return int
	 */
	public function getDownloadsAttribute()
	{
		// TODO: Read from statistics table
		return 0;
	}

	/**
	 * Get the User uploader of the Addon.
	 *
	 * @return App\Models\User
	 */
	public function getUploaderAttribute()
	{
		return $this->version->file->uploader;
	}

	/**
	 * Get the file name of the Addon.
	 *
	 * @return string
	 */
	public function getFilenameAttribute()
	{
		return $this->version->file->download_name;
	}

	/**
	 * Get the CRC calculation of the Addon.
	 *
	 * @return string
	 */
	public function getCrcAttribute()
	{
		return '-1';
	}

	/**
	 * Get the download link of the Addon.
	 *
	 * @return string
	 */
	public function getDownloadLinkAttribute()
	{
		return '/api/mod/'.$this->slug.'.zip';
	}

	/////////////////
	//* Utilities *//
	/////////////////
	/**
	 * Calculates metric bytes prefixes.
	 *
	 * @param  int  $bytes
	 * @param  int  $precision
	 *
	 * @return string
	 */
	protected static function formatBytesMet($bytes, $precision = 2)
	{ 
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1000));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1000, $pow);

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	/**
	 * Calculates binary bytes prefixes.
	 *
	 * @param  int  $bytes
	 * @param  int  $precision
	 *
	 * @return string
	 */
	protected static function formatBytesBin($bytes, $precision = 2)
	{ 
		$units = array('B', 'KiB', 'MiB', 'GiB', 'TiB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= (1 << (10 * $pow));

		return round($bytes, $precision) . ' ' . $units[$pow];
	}
}
