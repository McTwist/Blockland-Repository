<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Repository\Blockland\Addon\File as AddonFile;

class VersionCache extends Model
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Database Table associated with this Model.
	 *
	 * @var string
	 */
	protected $table = 'version_cache';

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
	protected $fillable = ['summary', 'authors', 'crc'];

	/////////////
	//* Magic *//
	/////////////
	/**
	 * Updates the VersionCache with data from File.
	 *
	 * @return void
	 */
	public function refresh()
	{
		$file = $this->file;

		if (!$file->createTempFile())
			return false;

		// Get crc value
		$this->crc = Blacklist\AddonCrcBlacklist::convertTo32(crc32($file->getTempContents()));

		$addon = new AddonFile($file->download_name);

		if (!$addon->Open(temp_path($file->path)))
		{
			$file->deleteTempFile();
			return false;
		}

		// Get internal data info
		$this->summary = $addon->Description();
		$this->authors = $addon->Authors('');

		$addon->Close();

		$file->deleteTempFile();

		return true;
	}

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Version that this VersionCache belongs to.
	 *
	 * @return Relationship
	 */
	public function version()
	{
		return $this->belongsTo(Version::class);
	}

	/**
	 * Returns the File that this VersionCache is connected to.
	 *
	 * @return Relationship
	 */
	public function file()
	{
		return $this->version->file();
	}
}
