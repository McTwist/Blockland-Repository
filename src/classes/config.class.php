<?php

class Config
{
	private static $config = [];

	// Load a configuration file
	public static function Load($file)
	{
		// Get file content
		$json = file_get_contents($file);
		if ($json === false)
			return false;

		// Decode preferences
		$config = json_decode($json);
		if ($config === null)
			return false;

		self::$config = $config;

		return true;
	}

	public static function Database()
	{
		return isset(self::$config->database) ? self::$config->database : null;
	}

	public static function Password()
	{
		return isset(self::$config->password) ? self::$config->password : null;
	}
}

?>