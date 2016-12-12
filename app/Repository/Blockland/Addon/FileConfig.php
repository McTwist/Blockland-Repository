<?php

namespace App\Repository\Blockland\Addon;

use App\Repository\Archive\ArchiveFile;

/*
 * FileConfig
 * Handles the config files
 */
class FileConfig extends ArchiveFile
{
	private $type = null;
	private $variables = array();
	private $lists = array();

	// Errors
	private $errorInvalidVariable = false;

	const NL = File::NL;

	public function __construct($archive, $filename)
	{
		parent::__construct($archive, $filename);

		$this->AddAttribute('content', null, function($value) { $this->ParseContent($value); });
		$this->AddAttribute('variables', function() { return $this->variables; }, null);
		$this->AddAttribute('lists', function() { return $this->lists; }, null);
	}

	// Read the config information
	protected function ParseContent($content)
	{
		// Split it into suitable pieces
		$lines = preg_split('/$\R?^/m', $content);

		foreach ($lines as $line)
		{
			$line = ltrim($line);
			// Empty line
			if (empty($line))
				continue;
			// Comment
			if (substr($line, 0, 2) == '//')
				continue;

			list($var, $value) = preg_split('/\s+/', $line, 2);

			// Normal variable
			if ($var[0] == '$')
			{
				$vars = $this->ParseVariable($var);
				if ($vars !== false)
				{
					// Set chain of array branches
					$node = &$this->variables;
					foreach ($vars as $key)
					{
						// Make sure array exists
						if (!array_key_exists($key, $node))
							$node[$key] = array();
						// Move aside the value
						elseif (!is_array($node[$key]))
							$node[$key] = array($node[$key]);
						// Iterate node
						$node = &$node[$key];
					}
					// Save variable
					if (is_array($node) && count($node) != 0)
						array_unshift($node, $value);
					else
						$node = $value;
				}
				// Variable was not parsed properly, so mark this as a failure
				else
				{
					$this->errorInvalidVariable = true;
				}
			}
			// Lists
			else
			{
				if (!array_key_exists($var, $this->lists))
					$this->lists[$var] = array();
				$this->lists[$var][] = $value;
			}
		}
	}

	// Validate the config
	public function Validate()
	{
		return !$this->errorInvalidVariable;
	}

	// Get one variable
	public function GetVariable($key)
	{
		$var = $this->ParseVariable($key);
		if ($var === false)
			return null;
		// Set chain of array branches
		$node = &$this->variables;
		foreach ($var as $key)
		{
			// Make sure that it even exists
			if (!array_key_exists($key, $node))
				return null;
			$node = &$node[$key];
		}
		// Handle node variable
		return (is_array($node) && array_key_exists(0, $node)) ? $node[0] : $node;
	}

	// Get one list
	public function GetList($key)
	{
		return array_key_exists($key, $this->lists) ? $this->lists[$key] : null;
	}

	// Get variable information
	private function ParseVariable($var)
	{
		$ret = preg_match_all('/([A-Za-z0-9_]+)/', $var, $matches);
		if ($ret !== false)
			return $matches[0];
		return false;
	}
}

?>
