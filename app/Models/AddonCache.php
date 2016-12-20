<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonCache extends Model
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Database Table associated with this Model.
	 *
	 * @var string
	 */
	protected $table = 'addon_cache';

	/**
	 * The Attributes that are hidden.
	 *
	 * @var string
	 */
	protected $hidden = ['id', 'version_id'];

	/**
	 * The Attributes that are allowed to be Mass Assigned.
	 *
	 * @var array
	 */
	protected $fillable = ['crc'];

	/////////////
	//* Magic *//
	/////////////
	/**
	 * Updates the AddonCache with data from File.
	 *
	 * @return bool
	 */
	public function refresh()
	{
		$file = $this->file;

		if (!$file->createTempFile())
			return false;

		// Get crc value
		$this->crc = Blacklist\AddonCrcBlacklist::convertTo32(crc32($file->getTempContents()));

		$file->deleteTempFile();

		return true;
	}

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Version that this AddonCache belongs to.
	 *
	 * @return Relationship
	 */
	public function version()
	{
		return $this->belongsTo(Version::class);
	}

	/**
	 * Returns the File that this AddonCache is connected to.
	 *
	 * @return Relationship
	 */
	public function file()
	{
		return $this->version->file();
	}
}
