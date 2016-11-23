<?php

namespace App\Repository;

/*
 * UID
 * Transforms numbers to unique identifiers through
 * a list of characters
 */

use Hashids\Hashids;

class UID
{
	// Amount of characters required
	static private $num_characters = 0;

	// Characters used
	static private $characters = '';

	// Key password that will be used to hide the real number
	static private $key = '';

	// Hashing routine
	static private $hash = null;

	static public function LoadConfig()
	{
		// Only load this once
		static $once = false;
		if ($once)
			return;
		$once = true;
		self::$num_characters = config('uid.num_characters');
		self::$characters = config('uid.characters');
		self::$key = config('uid.key');
	}

	static public function SetMinimum($num_characters)
	{
		self::Reset();
		self::$num_characters = $num_characters;
	}

	static public function SetCharacters($characters)
	{
		self::Reset();
		self::$characters = $characters;
	}

	static public function SetKey($key)
	{
		self::Reset();
		self::$key = $key;
	}

	// Translates a number to an uid
	static public function GetUID($num)
	{
		$uid = self::singleton()->encode($num);
		return empty($uid) ? null : $uid;
	}

	// Translates an uid to a number
	static public function GetNum($uid)
	{
		return self::singleton()->decode($uid);
	}

	// Highest recommended
	static public function Highest()
	{
		return self::singleton()->get_max_int_value();
	}

	// If for some strange reason a reset is required
	static private function Reset()
	{
		self::$hash = null;
	}

	// Is created once
	static private function singleton()
	{
		if (!is_object(self::$hash))
			self::$hash = new Hashids(
				self::$key, self::$num_characters, self::$characters);
		return self::$hash;
	}
}

// Load needed configs
UID::LoadConfig();

?>