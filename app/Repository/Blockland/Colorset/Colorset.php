<?php

namespace App\Repository\Blockland\Colorset;

class Colorset
{
	private $colors = [];
	private $rows = 0;
	private $cols = 0;

	// Load colorset from string
	public function LoadString($content)
	{
		$this->colors = [];

		$lines = preg_split('/$\R?^/m', $content);

		array_walk($lines, function(&$value, $i) { $value = trim($value); });

		$current = [];
		$rows = 0;
		$cols = 0;

		foreach ($lines as $line)
		{
			$line = trim($line);
			// Division
			if (strtoupper(substr($line, 0, 4)) === 'DIV:')
			{
				$name = trim(substr($line, 4));

				$colors = new \stdClass();

				$colors->name = $name;
				$colors->colors = $current;

				array_push($this->colors, $colors);

				$current = [];

				$cols++;
				$rows = 0;
			}
			// Colors
			elseif (!empty($line) && substr($line, 0, 2) !== '//')
			{
				$values = preg_split('/\s+/', $line, 5);

				$name = (count($values) > 4) ? trim(substr($values[4], 2)) : '';

				$color = new Color(
					$name,
					$values[0] + 0,
					$values[1] + 0,
					$values[2] + 0,
					$values[3] + 0);

				array_push($current, $color);

				$rows++;
			}

			// Update values
			if ($rows > $this->rows)
				$this->rows = $rows;
			if ($cols > $this->cols)
				$this->cols = $cols;
		}

		// Put in the rest of them
		if (count($current) > 0)
		{
			$colors = new \stdClass();

			$colors->name = 'Unknown';
			$colors->colors = $current;
			$this->cols++;

			array_push($this->colors, $colors);
		}
	}

	public function Get()
	{
		return $this->colors;
	}

	// Calculate distance for direct values
	// Note: Not all of these take in account of alpha value
	// Note2: A comparison was made, but it was a tough choice.
	// To resemble Blockland, that version could be used to keep consistency
	public function FindClosestColorValue(Color $color)
	{
		return $this->FindClosestColor($color, 'self::DistanceBlockland');
	}

	public function FindClosestColorProportion(Color $color)
	{
		return $this->FindClosestColor($color, 'self::DistanceSimplified');
	}

	public function FindClosestColorProportion2(Color $color)
	{
		return $this->FindClosestColor($color, 'self::DistanceCIE76');
	}

	public function FindClosestColorBest(Color $color)
	{
		return $this->FindClosestColor($color, 'self::DistanceCIE94');
	}

	public function FindClosestColorPerfect(Color $color)
	{
		return $this->FindClosestColor($color, 'self::DistanceCIEDE2000');
	}

	public function FindClosestColorAll(Color $color)
	{
		return $this->FindClosestColor($color, 'self::DistanceAll');
	}

	private function FindClosestColor(Color $color, $distance)
	{
		$closest = PHP_INT_MAX;
		$found = null;

		// Go through groups
		foreach ($this->colors as $group)
		{
			// Go through colors
			foreach ($group->colors as $col)
			{
				// Calculate distances
				$d = call_user_func($distance, $color, $col);
				if ($d < $closest)
				{
					$closest = $d;
					$found = $col;
				}
			}
		}

		return $found;
	}

	// Mix together the three most promising algorithms
	private static function DistanceAll(Color $a, Color $b)
	{
		return self::DistanceCIE76($a, $b) +
			self::DistanceCIE94($a, $b) +
			self::DistanceCIEDE2000($a, $b);
	}

	// This is how Blockland does it
	private static function DistanceBlockland(Color $a, Color $b)
	{
		$adiff = pow($a->AF() - $b->AF(), 2);
		return (pow($a->RF() - $b->RF(), 2) + pow($a->GF() - $b->GF(), 2) + pow($a->BF() - $b->BF(), 2)) + $adiff;
	}

	// Simplified LAB version
	private static function DistanceSimplified(Color $a, Color $b)
	{
		list($aL, $aa, $ab) = self::RGB2LAB($a->RF(), $a->GF(), $a->BF());
		list($bL, $ba, $bb) = self::RGB2LAB($b->RF(), $b->GF(), $b->BF());

		$Ldiff = abs($aL - $bL);
		$adiff = abs($aa - $ba);
		$bdiff = abs($ab - $bb);
		$alphadiff = abs($a->AF() - $b->AF());

		return ($Ldiff + $adiff + $bdiff) + $alphadiff;
	}

	// https://en.wikipedia.org/wiki/Color_difference#CIE76
	private static function DistanceCIE76(Color $a, Color $b)
	{
		list($aL, $aa, $ab) = self::RGB2LAB($a->RF(), $a->GF(), $a->BF());
		list($bL, $ba, $bb) = self::RGB2LAB($b->RF(), $b->GF(), $b->BF());
		$alphadiff = pow($a->AF() - $b->AF(), 2);

		return sqrt((pow($aL - $bL, 2) + pow($aa - $ba, 2) + pow($ab - $bb, 2)));
	}

	// https://en.wikipedia.org/wiki/Color_difference#CIE94
	private static function DistanceCIE94(Color $a, Color $b)
	{
		list($aL, $aa, $ab) = self::RGB2LAB($a->RF(), $a->GF(), $a->BF());
		list($bL, $ba, $bb) = self::RGB2LAB($b->RF(), $b->GF(), $b->BF());
		$alphadiff = pow($a->AF() - $b->AF(), 2);

		// Constants
		$KL = 3.0;
		$K1 = 0.045;
		$K2 = 0.015;

		// Do the calculations
		$deltaL = $aL - $bL;
		$deltaA = $aa - $ba;
		$deltaB = $ab - $bb;

		$c1 = sqrt($aa * $aa + $ab * $ab);
		$c2 = sqrt($ba * $ba + $bb * $bb);
		$deltaC = $c1 - $c2;

		$deltaH = $deltaA * $deltaA + $deltaB * $deltaB - $deltaC * $deltaC;
		$deltaH = $deltaH < 0 ? 0 : sqrt($deltaH);

		$sl = 1.0;
		$kc = 1.0;
		$kh = 1.0;

		$sc = 1.0 + $K1 * $c1;
		$sh = 1.0 + $K2 * $c1;

		$i = pow($deltaL / ($KL * $sl), 2) + pow($deltaC / ($kc * $sc), 2) + pow($deltaH / ($kh * $sh), 2);

		$final = $i < 0 ? 0 : sqrt($i);

		return $final;
	}

	// https://en.wikipedia.org/wiki/Color_difference#CIEDE2000
	private static function DistanceCIEDE2000(Color $a, Color $b)
	{
		list($aL, $aa, $ab) = self::RGB2LAB($a->RF(), $a->GF(), $a->BF());
		list($bL, $ba, $bb) = self::RGB2LAB($b->RF(), $b->GF(), $b->BF());
		$alphadiff = pow($a->AF() - $b->AF(), 2);

		// Constants
		$kL = 0.5;
		$kC = 1.0;
		$kH = 1.0;
		$TAU = deg2rad(360);
		$PI = deg2rad(180);
		$pow25To7 = 6103515625;

		/*
		 * Step 1
		 */
		$C1 = sqrt(($aa * $aa) + ($ab * $ab));
		$C2 = sqrt(($ba * $ba) + ($bb * $bb));

		$barC = ($C1 + $C2) / 2;
		$powBarC = pow($barC, 7);

		$G = 0.5 * (1 - sqrt($powBarC / ($powBarC + $pow25To7)));

		$a1Prime = (1 + $G) * $aa;
		$a2Prime = (1 + $G) * $ba;

		$CPrime1 = sqrt(($a1Prime * $a1Prime) + ($ab * $ab));
		$CPrime2 = sqrt(($a2Prime * $a2Prime) + ($bb * $bb));

		$hPrime1 = 0;
		if ($ab != 0 || $a1Prime != 0)
		{
			$hPrime1 = atan2($ab, $a1Prime);
			if ($hPrime1 < 0)
				$hPrime1 += $TAU;
		}

		$hPrime2 = 0;
		if ($bb != 0 || $a2Prime != 0)
		{
			$hPrime2 = atan2($bb, $a2Prime);
			if ($hPrime2 < 0)
				$hPrime2 += $TAU;
		}

		/*
		 * Step 2
		 */
		$deltaLPrime = $bL - $aL;

		$deltaCPrime = $CPrime2 - $CPrime1;

		$deltahPrime = 0;
		$CPrimeProduct = $CPrime1 * $CPrime2;
		if ($CPrimeProduct != 0)
		{
			$deltahPrime = $hPrime2 - $hPrime1;
			if ($deltahPrime < -$PI)
				$deltahPrime += $TAU;
			else if ($deltahPrime > $PI)
				$deltahPrime -= $TAU;
		}

		$deltaHPrime = 2 * sqrt($CPrimeProduct) * sin($deltahPrime / 2);

		/*
		 * Step 3
		 */
		$barLPrime = ($aL + $bL) / 2;

		$barCPrime = ($CPrime1 + $CPrime2) / 2;

		$hPrimeSum = $hPrime1 + $hPrime2;
		$barhPrime = $hPrimeSum;
		if ($CPrimeProduct == 0)
		{
			if (fabs($hPrime1 - $hPrime2) <= $PI)
			{
				$barhPrime = $hPrimeSum / 2;
			}
			else
			{
				$barhPrime = ($hPrimeSum < $TAU) ? ($hPrimeSum + $TAU) / 2 : ($hPrimeSum - $TAU) / 2;
			}
		}

		$T = 1 - (0.17 * cos($barhPrime - deg2rad(30))) +
			(0.24 * cos(2.0 * $barhPrime)) +
			(0.32 * cos((2 * $barhPrime) + deg2rad(6))) -
			(0.20 * cos((4 * $barhPrime) - deg2rad(63)));

		$deltaTheta = deg2rad(30) * exp(-pow(($barhPrime - deg2rad(275)) / deg2rad(25), 2));

		$powBarCPrime = pow($barCPrime, 7);
		$RC = 2 * sqrt($powBarCPrime / ($powBarCPrime + $pow25To7));

		$powBarLPrime = pow($barLPrime - 50, 2);
		$SL = 1 + ((0.015 * $powBarLPrime) / sqrt(20 + $powBarLPrime));

		$SC = 1 + (0.045 * $barCPrime);

		$SH = 1 + (0.015 * $barCPrime * $T);

		$RT = (-sin(2 * $deltaTheta)) * $RC;

		$deltaE = sqrt(
			pow($deltaLPrime / ($kL * $SL), 2) +
			pow($deltaCPrime / ($kC * $SC), 2) +
			pow($deltaHPrime / ($kH * $SH), 2) +
			($RT * ($deltaCPrime / ($kC * $SC)) * ($deltaHPrime / ($kH * $SH))));

		return $deltaE;
	}

	// A simple conversion from RGB to LAB
	private function RGB2LAB($r, $g, $b)
	{
		// To XYZ
		$r = ($r > 0.04045) ? pow(($r + 0.055) / 1.055, 2.4) : $r / 12.92;
		$g = ($g > 0.04045) ? pow(($g + 0.055) / 1.055, 2.4) : $g / 12.92;
		$b = ($b > 0.04045) ? pow(($b + 0.055) / 1.055, 2.4) : $b / 12.92;

		$r *= 100;
		$g *= 100;
		$b *= 100;

		$X = $r * 0.4124 + $g * 0.3576 + $b * 0.1805;
		$Y = $r * 0.2126 + $g * 0.7152 + $b * 0.0722;
		$Z = $r * 0.0193 + $g * 0.1192 + $b * 0.9505;

		// To Lab
		$X = $X / 95.047;
		$Y = $Y / 100.000;
		$Z = $Z / 108.883;

		$X = ($X > 0.008856) ? pow($X, 1 / 3) : (7.787 * $X) + (16 / 116);
		$Y = ($Y > 0.008856) ? pow($Y, 1 / 3) : (7.787 * $Y) + (16 / 116);
		$Z = ($Z > 0.008856) ? pow($Z, 1 / 3) : (7.787 * $Z) + (16 / 116);

		$L = (116 * $Y) - 16;
		$a = 500 * ($X - $Y);
		$b = 200 * ($Y - $Z);

		return array($L, $a, $b);
	}

	public function PrintImage($bg = true, $file = null)
	{
		if (!extension_loaded('gd'))
			return;

		$block_width = 64;
		$block_height = 64;

		// Calculate sizes
		$width = $this->cols * $block_width;
		$height = $this->rows * $block_height;

		$block = imagecreatetruecolor($block_width, $block_height);
		imagealphablending($block, false);
		imagesavealpha($block, true);

		$img = imagecreatetruecolor($width, $height);
		imagealphablending($img, false);
		imagesavealpha($img, true);

		// Fill with background
		if ($bg === false)
		{
			imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
		}
		else
		{
			$back = imagecreatefrompng($bg === true ? storage_path('app/public/back_colorset.png') : $bg);
			imagesettile($img, $back);
			imagefilledrectangle($img, 0, 0, $width, $height, IMG_COLOR_TILED);
			imagedestroy($back);
		}

		$x = 0;
		$y = 0;

		// Iterate groups
		foreach ($this->colors as $group)
		{
			// Iterate colors
			foreach ($group->colors as $color)
			{
				$color1 = imagecolorallocatealpha($block, 
					$color->R(),
					$color->G(),
					$color->B(),
					// Alpha is strange
					($bg ? 127 - ($color->A() >> 1) : 0));

				imagefill($block, 0, 0, $color1);

				imagealphablending($img, true);
				imagecopyresampled($img, $block, $x, $y, 0, 0, 
					$block_width, $block_height, $block_width, $block_height);
				imagealphablending($img, false);

				imagecolordeallocate($block, $color1);

				$y += $block_height;
			}

			$y = 0;
			$x += $block_width;
		}

		if ($file === null)
		{
			header('Content-type:image/png');
			imagepng($img);
		}
		else
		{
			imagepng($img, $file);
		}

		imagedestroy($img);
		imagedestroy($block);
	}
}

?>