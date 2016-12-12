<?php
namespace App\Repository\Archive;

trait ArchiveAttributes
{
	private $attribute_get = [];
	private $attribute_set = [];

	// Add an attribute that can be set and get
	public function AddAttribute($key, $get, $set)
	{
		if (is_callable($get, true))
		{
			if (!array_key_exists($key, $this->attribute_get))
				$this->attribute_get[$key] = [];
			$this->attribute_get[$key][] = $get;
		}
		if (is_callable($set, true))
		{
			if (!array_key_exists($key, $this->attribute_set))
				$this->attribute_set[$key] = [];
			$this->attribute_set[$key][] = $set;
		}
	}

	// Get an attribute
	public function __get($key)
	{
		if (!array_key_exists($key, $this->attribute_get))
		{
			trigger_error("Undefined property: {$key}", E_USER_NOTICE);
			return null;
		}

		// Call all the functions, sending in the old return each time
		$ret = null;
		foreach ($this->attribute_get[$key] as $func)
			$ret = call_user_func($func, $ret);

		return $ret;
	}

	// Set an attribute
	public function __set($key, $value)
	{
		if (!array_key_exists($key, $this->attribute_set))
		{
			trigger_error("Undefined property: {$key}", E_USER_NOTICE);
			return;
		}

		// Call all the functions
		foreach ($this->attribute_set[$key] as $func)
			call_user_func($func, $value);
	}

	// Check if an attribute is set
	public function __isset($key)
	{
		return array_key_exists($key, $this->attribute_get);
	}
}

?>
