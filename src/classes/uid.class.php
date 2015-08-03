<?php
/*
 * UID
 * Transforms numbers to unique identifiers through
 * a list of characters
 */

require_once 'config.class.php';

require 'third/hashids/HashGenerator.php';
require 'third/hashids/Hashids.php';

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
		$pref = Config::Uid();
		if (isset($pref->num_characters))
			self::$num_characters = $pref->num_characters;
		if (isset($pref->characters))
			self::$characters = $pref->characters;
		if (isset($pref->key))
			self::$key = $pref->key;
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
			self::$hash = new Hashids\Hashids(
				self::$key, self::$num_characters, self::$characters);
		return self::$hash;
	}
}

?>