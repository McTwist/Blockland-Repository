<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\FormatSize;
use App\Repository\Blockland\Addon\File as AddonFile;

class Addon extends Model
{
	//////////////
	//* Traits *//
	//////////////
	use FormatSize;

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

	// TODO: Add slug version that will determine what version to take
	// slug_version = version | channel.version | slug_version | slug_channel.version

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
			foreach ($addon->channels as $channel)
				$channel->delete();
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
	 * Sets the default Channel that this Addon has.
	 *
	 * @return void
	 */
	public function setChannelAttribute($channel)
	{
		if ($channel->default)
			return;
		if (!$this->channels()->contains($channel))
			return;
		
		$channel->makeDefault();
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

	/**
	 * Returns the Tag that this Addon has.
	 *
	 * @return Relationship
	 */
	public function tags()
	{
		return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
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
	 * Updates the File with data from Addon and its relations.
	 *
	 * @return bool
	 */
	public function flush()
	{
		// Get needed models
		$channel = $this->channel;
		$version = $this->version;
		$cache = $version->cache;
		$file = $version->file;

		if (!$file->createTempFile())
			return false;

		// Load the addon
		$addon = new AddonFile($file->download_name);

		if (!$addon->Open(temp_path($file->path)))
		{
			$file->deleteTempFile();
			return false;
		}

		// Set internal data info
		$addon->Title($this->name);
		$addon->Authors($cache->authors);
		$addon->Description($cache->summary);
		$addon->Channel($channel->name);
		$addon->Version($version->name);

		// Set repository info
		$addon->SetRepository(url('api'), 'json', $this->slug);

		$addon->Cleanup();

		$addon->Close();

		// Update crc value
		$crc = Blacklist\AddonCrcBlacklist::convertTo32(crc32($file->getTempContents()));
		$cache->crc = $crc;
		$cache->save();

		// Send to storage
		$file->saveTempFile();
		$file->deleteTempFile();
		$file->update();

		return true;
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
		return ($cache = $this->version->cache) ? $cache->summary : '';
	}

	/**
	 * Set the summary of the Addon.
	 *
	 * @return void
	 */
	public function setSummaryAttribute($summary)
	{
		if ($cache = $this->version->cache)
			$cache->update(['summary' => $summary]);
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
	 * Returns the authors that made this Addon.
	 *
	 * @return string
	 */
	public function getAuthorsAttribute()
	{
		return ($cache = $this->version->cache) ? $cache->authors : '';
	}

	/**
	 * Sets the authors that made this Addon.
	 *
	 * @return void
	 */
	public function setAuthorsAttribute($authors)
	{
		if ($cache = $this->version->cache)
			$cache->update(['authors' => $authors]);
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
		return ($cache = $this->version->cache) ? $cache->crc : '';
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
