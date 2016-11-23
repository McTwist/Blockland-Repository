<?php

namespace App\Models;

use App\Models\Blacklist\AddonCrcBlacklist;
use App\Models\Blacklist\AddonNameBlacklist;

class AddonBlacklist
{
	/**
	 * List of blacklists to check towards.
	 *
	 * @var array
	 */
	protected $lists = [
		AddonCrcBlacklist::class,
		AddonNameBlacklist::class,
	];

	/**
	 * Checks if the value exists in any of the blacklists.
	 *
	 * @param any $value
	 * @return boolean
	 */
	public function has($value)
	{
		// Iterate lists
		foreach ($this->lists as $list)
		{
			// Check if exists
			if ($list::got($value)->first() !== null)
				return true;
		}
		return false;
	}

	/**
	 * Add a new value to blacklists if they does not exists.
	 *
	 * @param any $value
	 * @return boolean
	 */
	public function add($value)
	{
		$ret = false;

		// A bunch of them
		if (is_array($value))
		{
			// Avoid doing a fetch
			if (empty($value))
				return false;

			// Prepare variables
			$values = [];
			$checks = [];

			// Get lists
			foreach ($this->lists as $i => $list)
			{
				$checks[$i] = $list::values()->toArray();
			}

			// Iterate values
			foreach ($value as $v)
			{
				// Iterate lists
				foreach ($this->lists as $i => $list)
				{
					// Ensure the value is valid for this list
					if ($list::validValue($v))
					{
						// Prepare for further use
						$v = $list::prepare($v);
						// Check for exists
						if (!in_array($v, $checks[$i]))
						{
							$values[$i][] = $v;
							// Finished saving this value
							break;
						}
					}
				}
			}

			// Add to lists
			foreach ($this->lists as $i => $list)
			{
				$v = &$values[$i];
				// Avoid inserting nothing
				if (count($v))
					$ret |= $list::insertValues($v);
			}
		}
		// Just one
		else
		{
			// Iterate lists
			foreach ($this->lists as $list)
			{
				// Ensure the value is valid for this list
				if ($list::validValue($value))
				{
					// Check if exists
					if ($list::got($value)->first() === null)
					{
						$ret |= $list::insertValues([$value]);
						// Finished
						break;
					}
				}
			}
		}

		return $ret;
	}

	/**
	 * Parses a script file.
	 *
	 * @param string $file
	 * @return boolean
	 */
	public function parseScript($file)
	{
		return $this->parseScriptString(file_get_contents($file));
	}

	/**
	 * Parses a script string.
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function parseScriptString($str)
	{
		// Split lines
		$lines = preg_split('/$\R?^/m', $str);

		$found = [];

		// Iterate lines
		foreach ($lines as $line)
		{
			$line = trim($line);

			// Find specific CrapOn list
			if (preg_match('/^\$CrapOn([A-Za-z]*)_(.*) = 1;$/', $line, $matches) !== 1)
				continue;

			$found[] = bl_convert_encoding($matches[2]);
		}

		// Add all new to database
		$this->add($found);

		return true;
	}
}
