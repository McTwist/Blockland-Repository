<?php

namespace App\Repository\Blockland\Addon;

use App\Repository\Archive\ArchiveFile;

/*
 * FileNamecheck
 * Handles the name checking
 */
class FileNamecheck extends ArchiveFile
{
	private $filebase = '';
	private $namecheck = '';

	public function __construct($archive_name, $filename)
	{
		$this->filebase = basename($archive_name, '.zip');
	}

	public function Read($content)
	{
		$this->namecheck = $content;
	}

	// Validate the name
	public function Validate()
	{
		return $this->namecheck === $this->filebase;
	}

	// Generate a new namecheck
	public function Write()
	{
		// Ignore everything and just add the filename as it should be
		return $this->namecheck = $this->filebase;
	}

	public function Namecheck()
	{
		return $this->namecheck;
	}
}

?>