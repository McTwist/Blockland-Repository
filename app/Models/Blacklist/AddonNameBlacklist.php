<?php

namespace App\Models\Blacklist;

use Illuminate\Database\Eloquent\Model;

class AddonNameBlacklist extends Model implements AddonBlacklistInterface
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Database Table associated with this Model.
	 *
	 * @var string
	 */
	protected $table = 'addon_name_blacklist';

	/**
	 * The Attributes that are allowed to be Mass Assigned.
	 *
	 * @var array
	 */
	protected $fillable = ['name'];

	/////////////////
	//* Interface *//
	/////////////////
	/**
	 * Inserts a list of values.
	 *
	 * @param array $values
	 * @return bool
	 */
	public static function insertValues(array $values)
	{
		foreach ($values as &$value)
		{
			$value = ['name' => $value];
		}

		return self::insert($values);
	}

	/**
	 * Returns all values from AddonCrcBlacklist.
	 *
	 * @return Collection
	 */
	public static function values()
	{
		return static::pluck('name');
	}

	/**
	 * Checks if value is valid.
	 *
	 * @param unknown $value
	 * @return bool
	 */
	public static function validValue($value)
	{
		return preg_match('/[A-Za-z]*_.*/', $value) === 1;
	}

	/**
	 * Prepares the value.
	 *
	 * @param unknown $value
	 * @return unknown
	 */
	public static function prepare($value)
	{
		return $value;
	}

	//////////////
	//* Scopes *//
	//////////////
	/**
	 * Returns a value if it got it.
	 *
	 * @param string $name
	 * @return Builder
	 */
	public function scopeGot($query, $name)
	{
		return $this->where('name', $name);
	}

	///////////////////////////
	//* Attribute Overrides *//
	///////////////////////////
	/**
	 * Returns the value.
	 *
	 * @return string
	 */
	public function getValueAttribute()
	{
		return $this->name;
	}
}
