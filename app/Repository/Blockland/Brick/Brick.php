<?php

namespace App\Repositiory\Blockland\rick;

use App\Repository\Archive\ArchiveFile;

// https://forum.blockland.us/index.php?topic=53716.0

class Brick extends ArchiveFile
{
	private $valid = true;

	public $x; // length (studs)
	public $y; // width (studs)
	public $z; // height (plates)
	public $special = false;

	// Special
	public $brickGrid = null;
	public $coverageHide = null;
	public $coverageArea = null;

	const NL = "\r\n";

	// Read brick
	public function Read($content)
	{
		$this->valid = true;

		$content = explode(self::NL, $content);
		$rows = count($content);

		// First line: Size
		list($this->x, $this->y, $this->z) = explode(' ', array_shift($content));

		// Second line: Type
		$line = array_shift($content);
		if ($line == 'BRICK')
		{
			// Ordinary brick, so don't do anything else
			return;
		}
		// Required to be special
		elseif ($line != 'SPECIAL')
		{
			return $this->Invalidate();
		}
		$this->special = true;

		// TODO: Continue to read to create a model to be displayed
		// Note: The parsing is quite lack, so it's easy to screw things up

		// Brick grid
		$this->brickGrid = array();
		for ($y = 0; $y < $this->y; ++$y)
		{
			array_shift($content); // Empty line
			for ($z = 0; $z < $this->z; ++$z)
			{
				$x = 0;
				$line = array_shift($content);
				// Validate size
				if (strlen($line) != $this->x)
				{
					return $this->Invalidate();
				}

				foreach (str_split($line) as $value)
				{
					// Validate type
					switch ($value)
					{
					case 'X':
					case 'u':
					case 'd':
					case '-':
					case 'b':
						$this->brickGrid[$x][$y][$z] = $value;
						break;
					default:
						return $this->Invalidate();
					}
					++$x;
				}
			}
		}

		array_shift($content);

		// Coverage
		if (array_shift($content) != '0')
		{
			return $this->Invalidate();
		}
		array_shift($content); // COVERAGE: // TBNESW
		$this->coverageHide = array_fill(0, 6, true);
		$this->coverageArea = array_fill(0, 6, 1);

		for ($i = 0; $i < 6; ++$i)
		{
			$line = array_shift($content);
			list($hide, $area) = array_map('trim', str_split($line, ':'));
			$this->coverageHide[] = !!$hide;
			$this->coverageArea[] = $area;
		}

		// Quads

		// Tex

		// Position

		// Colors

		// UV-coords

		// Normals


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

	private function Invalidate()
	{
		$this->valid = false;
	}
}

?>