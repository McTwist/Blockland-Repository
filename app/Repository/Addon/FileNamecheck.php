<?php

namespace App\Repository\Addon;

/*
 * FileNamecheck
 * Handles the name checking
 */
class FileNamecheck
{
	private $filename = '';
	private $namecheck = '';

	public function __construct($filename, $namecheck = '')
	{
		$this->filename = basename($filename, '.zip');
		$this->namecheck = $namecheck;
	}

	// Validate the name
	public function Validate()
	{
		return $this->namecheck === $this->filename;
	}

	// Generate a new namecheck
	public function Generate()
	{
		// Ignore everything and just add the filename as it should be
		return $this->namecheck = $this->filename;
	}

	public function Get()
	{
		return $this->namecheck;
	}
}

?>