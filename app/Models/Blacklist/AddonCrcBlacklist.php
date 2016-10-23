<?php

namespace App\Models\Blacklist;

use Illuminate\Database\Eloquent\Model;

class AddonCrcBlacklist extends Model implements AddonBlacklistInterface
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Database Table associated with this Model.
	 *
	 * @var string
	 */
	protected $table = 'addon_crc_blacklist';

	/**
	 * The Attributes that are allowed to be Mass Assigned.
	 *
	 * @var array
	 */
	protected $fillable = ['crc'];

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
			$value = ['crc' => $value];
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
		return static::pluck('crc');
	}

	/**
	 * Checks if value is valid.
	 *
	 * @param unknown $value
	 * @return bool
	 */
	public static function validValue($value)
	{
		return self::isCrc($value);
	}

	/**
	 * Prepares the value.
	 *
	 * @param unknown $value
	 * @return int
	 */
	public static function prepare($value)
	{
		return self::convertTo32($value);
	}

	//////////////
	//* Scopes *//
	//////////////
	/**
	 * Returns a value if it got it.
	 *
	 * @param int $crc
	 * @return Builder
	 */
	public function scopeGot($query, $crc)
	{
		if (self::isCrc($crc))
			$crc = self::convertTo32($crc);
		return $this->where('crc', $crc);
	}

	///////////////////////////
	//* Attribute Overrides *//
	///////////////////////////
	/**
	 * Returns the value.
	 *
	 * @return int
	 */
	public function getValueAttribute()
	{
		return $this->crc;
	}

	/////////////////
	//* Utilities *//
	/////////////////
	/**
	 * Checks if the string is a valid crc32.
	 *
	 * @param string $value
	 * @return boolean
	 */
	public static function isCrc($value)
	{
		return preg_match('/^-?[1-9][0-9]{0,9}$/', $value);
	}

	/**
	 * Converts an integer to 32-bit.
	 * This is to ensure that we can store it in the database and check towards it.
	 * Should work on a 32-bit architecture.
	 *
	 * @param int $value
	 * @return int
	 */
	public static function convertTo32($value)
	{
		// Ensure it is an integer
		if (!is_int($value))
			$value = intval($value);
		// Crop it to 32-bit
		$value &= 0xffffffff;
		// Make it signed
		return $value > 0x7fffffff ? $value - 0x100000000 : $value;
	}
}
