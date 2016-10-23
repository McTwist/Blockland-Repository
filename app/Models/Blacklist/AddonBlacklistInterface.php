<?php

namespace App\Models\Blacklist;

interface AddonBlacklistInterface
{
	/**
	 * Inserts a list of values.
	 *
	 * @param array $values
	 * @return bool
	 */
	public static function insertValues(array $values);

	/**
	 * Returns all values from AddonCrcBlacklist.
	 *
	 * @return Collection
	 */
	public static function values();

	/**
	 * Checks if value is valid.
	 *
	 * @param unknown $value
	 * @return bool
	 */
	public static function validValue($value);

	/**
	 * Prepares the value.
	 *
	 * @param unknown $value
	 */
	public static function prepare($value);
}
