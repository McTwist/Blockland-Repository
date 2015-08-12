<?php

class Filter
{
	// Filter through the username and return a valid one
	static public function Username($value)
	{
		return filter_var($value, FILTER_SANITIZE_STRING);
	}

	// Validate a BLID
	static public function BLID($value)
	{
		$opt = array(
			'options' => array(
				'default' => null,
				'min_range' => 0,
				'max_range' => 999999
			)
		);
		return filter_var($value, FILTER_VALIDATE_INT, $opt);
	}

	// Validate an e-mail
	static public function Email($value)
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL, self::DefaultOptions());
	}

	// Validate an IP-address
	static public function IP($value)
	{
		return filter_var($value, FILTER_VALIDATE_IP, self::DefaultOptions());
	}

	// Validate a URL
	static public function URL($value)
	{
		return filter_var($value, FILTER_VALIDATE_URL, self::DefaultOptions());
	}

	// Create a default array of options
	static private function DefaultOptions($default = null)
	{
		return array(
			'options' => array(
				'default' => $default
			)
		);
	}
}

?>