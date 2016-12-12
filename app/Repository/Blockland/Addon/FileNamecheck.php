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

	public function __construct($archive, $filename)
	{
		parent::__construct($archive, $filename);
		$this->filebase = basename($archive->filename, '.zip');

		// Ignore everything and just add the filename as it should be
		$this->AddAttribute('content', function() { return $this->namecheck = $this->filebase; }, function($value) { $this->namecheck = $value; });
		$this->AddAttribute('namecheck', function() { return $this->namecheck; }, null);
	}

	// Validate the name
	public function Validate()
	{
		return $this->namecheck === $this->filebase;
	}
}

?>
