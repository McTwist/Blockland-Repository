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
	private $colorset = new Colorset();

	// Read colorset
	public function Set($content)
	{
		$this->colorset->LoadString($content);
	}

	// Validate colorset
	public function Validate()
	{
		return true;
	}

	// Generate a colorset file
	public function Get()
	{
		// We do not create colorsets for now
		return null;
	}

	// Get all colors available
	public function GetColors()
	{
		return $this->colorset->Get();
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