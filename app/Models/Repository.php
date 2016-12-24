<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\FormatSize;
use App\Repository\Blockland\Addon\File as AddonFile;

class Repository extends Model
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
		static::deleting(function($repository)
		{
			foreach ($repository->channels as $channel)
				$channel->delete();
		});

		// Model was created
		static::created(function($repository)
		{
			$repository->createChannel('release', true);
		});
	}

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Channels that this Repository has.
	 *
	 * @return Relationship
	 */
	public function channels()
	{
		return $this->hasMany(Channel::class);
	}

	/**
	 * Returns the default Channel that this Repository has.
	 *
	 * @return Relationship
	 */
	public function channel()
	{
		return $this->hasOne(Channel::class)->where("default", true);
	}

	/**
	 * Returns the Versions that this Repository has.
	 *
	 * @return Relationship
	 */
	public function versions()
	{
		return $this->hasManyThrough(Version::class, Channel::class);
	}

	/**
	 * Returns the default Version that this Repository has.
	 *
	 * @return Relationship
	 */
	public function version()
	{
		return $this->channel->version();
	}

	/**
	 * Returns the Users that this Repository belongs to.
	 *
	 * @return Relationship
	 */
	public function owners()
	{
		return $this->belongsToMany(User::class)->withTimestamps();
	}

	/**
	 * Returns the Authors that made this Repository.
	 *
	 * @return Relationship
	 */
	public function authors()
	{
		return $this->version->authors();
	}

	/**
	 * Returns the RepositoryType that this Repository belongs to.
	 *
	 * @return Relationship
	 */
	public function type()
	{
		return $this->belongsTo(RepositoryType::class, 'repository_type_id');
	}

	/**
	 * Returns the Category that this Repository belongs to.
	 *
	 * @return Relationship
	 */
	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	/**
	 * Returns the Tag that this Repository has.
	 *
	 * @return Relationship
	 */
	public function tags()
	{
		return $this->belongsToMany(Tag::class)->withTimestamps();
	}

	/////////////////
	//* Utilities *//
	/////////////////
	/**
	 * Returns the slug that is unique to this Repository.
	 *
	 * @param string $slug The slug unique to Repository.
	 *
	 * @return string
	 */
	public static function fromSlug($slug)
	{
		return self::where('slug', $slug)->first();
	}

	/**
	 * Returns the Repository that is unique to this filename.
	 *
	 * @param string $file The file identifying this Repository.
	 *
	 * @return string
	 */
	public static function fromFile($file)
	{
		// Find out about the file
		if ($file = File::fromFilename($file))
		{
			// Get the version
			$version = $file->link;
			if ($version instanceof Version)
			{
				// And we're done with the link
				return $version->repository;
			}
		}
		return NULL;
	}

	/**
	 * Returns true if User are owner to this Repository.
	 *
	 * @param User $user The User to check ownership.
	 *
	 * @return boolean
	 */
	public function isOwner(User $user)
	{
		return $this->owners->contains($user);
	}

	/**
	 * Updates the File with data from Repository and its relations.
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
		$addon->title = $this->name;
		$addon->info->authors = $version->authors->pluck('name')->toArray();
		$addon->info->description = $version->summary;
		$addon->version->channel = $channel->name;
		$addon->version->version = $version->name;

		// Set repository info
		$addon->version->SetRepository(url('api'), 'json', $this->slug);

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

	/**
	 * Create a Channel and return it.
	 *
	 * @param string $name The name of the Channel.
	 * @param bool $default Set as default Version.
	 *
	 * @return Channel
	 */
	public function createChannel($name, $default = false)
	{
		$channel_obj = new Channel;
		$channel_obj->name = $name;
		$channel_obj->slug = $this->slug.'_'.str_slug($name);
		$this->channels()->save($channel_obj);
		if ($default)
			$channel_obj->default = $default;
		return $channel_obj;
	}

	///////////////////////////
	//* Attribute Overrides *//
	///////////////////////////
	// Note: All attributes should later on read from default version or defined slug
	/**
	 * Get the size of the Repository,
	 *
	 * @return string
	 */
	public function getSizeAttribute()
	{
		return $this->version->file->size;
	}

	/**
	 * Get the size in metric of the Repository.
	 *
	 * @return string
	 */
	public function getSizeMetAttribute()
	{
		return static::formatBytesMet($this->size);
	}

	/**
	 * Get the size in bin of the Repository.
	 *
	 * @return string
	 */
	public function getSizeBinAttribute()
	{
		return static::formatBytesBin($this->size);
	}

	/**
	 * Get the summary of the Repository.
	 *
	 * @return string
	 */
	public function getSummaryAttribute()
	{
		return $this->version->summary;
	}

	/**
	 * Set the summary of the Repository.
	 *
	 * @return void
	 */
	public function setSummaryAttribute($summary)
	{
		$this->version->update(['summary' => $summary]);
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
	 * Get the Version name of the Repository.
	 *
	 * @return string
	 */
	public function getVersionNameAttribute()
	{
		return $this->version->name;
	}

	/**
	 * Get amounts of downloads of the Repository.
	 *
	 * @return int
	 */
	public function getDownloadsAttribute()
	{
		// TODO: Read from statistics table
		return 0;
	}

	/**
	 * Get the User uploader of the Repository.
	 *
	 * @return App\Models\User
	 */
	public function getUploaderAttribute()
	{
		return $this->version->file->uploader;
	}

	/**
	 * Get the file name of the Repository.
	 *
	 * @return string
	 */
	public function getFilenameAttribute()
	{
		return $this->version->file->download_name;
	}

	/**
	 * Get the CRC calculation of the Repository.
	 *
	 * @return string
	 */
	public function getCrcAttribute()
	{
		return ($cache = $this->version->cache) ? $cache->crc : '';
	}

	/**
	 * Get the route link of the Repository.
	 *
	 * @return string
	 */
	public function getRouteAttribute()
	{
		return route($this->type->name.'.show', $this->slug);
	}

	/**
	 * Get the download link of the Repository.
	 *
	 * @return string
	 */
	public function getDownloadLinkAttribute()
	{
		return '/api/mod/'.$this->slug.'.zip';
	}
}
