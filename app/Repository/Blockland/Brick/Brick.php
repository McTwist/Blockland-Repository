<?php

namespace App\Repositiory\Blockland\rick;

use App\Repository\Archive\ArchiveFile;

// https://forum.blockland.us/index.php?topic=53716.0

class Brick extends ArchiveFile
{
	private $valid = true;

	public $x;
	public $y;
	public $z;
	public $special = false;

	const NL = "\r\n";

	// Read brick
	public function Read($content)
	{
		$this->valid = true;

		$content = explode(self::NL, $content);
		$rows = count($content);

		// First line: Size
		list($this->x, $this->x, $this->x) = explode(' ', $content[0]);

		// Second line: Type
		if ($content[1] == 'BRICK')
		{
			// Ordinary brick, so don't do anything else
			return;
		}
		// Required to be special
		elseif ($content[1] != 'SPECIAL')
		{
			$this->valid = false;
			return;
		}
		$this->special = true;

		// TODO: Continue to read to create a model to be displayed
	}

	// Validate brick
	public function Validate()
	{
		return $this->valid;
	}

	// Generate a brick
	public function Write()
	{
		return '';
	}
}

?>