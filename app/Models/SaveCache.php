<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaveCache extends Model
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Database Table associated with this Model.
	 *
	 * @var string
	 */
	protected $table = 'save_cache';

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
	protected $fillable = ['brick_count'];

	/////////////
	//* Magic *//
	/////////////
	/**
	 * Updates the SaveCache with data from File.
	 *
	 * @return bool
	 */
	public function refresh()
	{
		$file = $this->file;

		if (!$file->createTempFile())
			return false;

		// TODO: Count the bricks

		$file->deleteTempFile();

		return true;
	}

	/////////////////////
	//* Relationships *//
	/////////////////////
	/**
	 * Returns the Version that this SaveCache belongs to.
	 *
	 * @return Relationship
	 */
	public function version()
	{
		return $this->belongsTo(Version::class);
	}

	/**
	 * Returns the File that this SaveCache is connected to.
	 *
	 * @return Relationship
	 */
	public function file()
	{
		return $this->version->file();
	}
}
