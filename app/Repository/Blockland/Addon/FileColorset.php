<?php

namespace App\Repository\Blockland\Addon;

use App\Repository\Archive\ArchiveFile;

use App\Repository\Blockland\Colorset\Colorset;

/*
 * FileColorset
 * Handles the colorset file
 */
class FileColorset extends ArchiveFile
{
	private $colorset = null;

	public function __construct($archive, $filename)
	{
		parent::__construct($archive, $filename);
		$this->colorset = new Colorset();

		// Note: We do not create colorsets for now
		$this->AddAttribute('content', null, function($value) { $this->colorset->LoadString($value); });
		$this->AddAttribute('colors', function() { return $this->colorset->Get(); }, null);
	}

	// Validate colorset
	public function Validate()
	{
		return true;
	}

	// Get closest color
	public function FindClosestColor($color)
	{
		return $this->colorset->FindClosestColorValue($color);
	}

	// Create an image and put it into a file
	public function GenerateImage($file)
	{
		return $this->colorset->PrintImage(true, $file);
	}
}

?>
