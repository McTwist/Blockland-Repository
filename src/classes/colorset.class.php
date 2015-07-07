<?php

class Color
{
	private $name = '';
	private $r = 255;
	private $g = 255;
	private $b = 255;
	private $a = 255;

	public function __construct($name, $r = 255, $g = 255, $b = 255, $a = 255)
	{
		$this->name = $name;
		$this->R($r);
		$this->G($g);
		$this->B($b);
		$this->A($a);
	}

	public function R($r = null)
	{
		self::set_color($this->r, $r);
		return $this->r;
	}

	public function G($g = null)
	{
		self::set_color($this->g, $g);
		return $this->g;
	}

	public function B($b = null)
	{
		self::set_color($this->b, $b);
		return $this->b;
	}

	public function A($a = null)
	{
		self::set_color($this->a, $a);
		return $this->a;
	}

	public function Name()
	{
		return $this->name;
	}

	// Set correct color value
	private static function set_color(&$color, $val)
	{
		if (is_float($val))
			$color = (int)($val * 255);
		elseif (is_int($val))
			$color = $val;

		self::clamp($color);
	}

	// Clamp the value
	private static function clamp(&$val, $min = 0, $max = 255)
	{
		if ($val < $min)
			$val = $min;
		elseif ($val > $max)
			$val = $max;
	}
}

class Colorset
{
	private $colors = [];

	// Load colorset from string
	public function LoadString($content)
	{
		$this->colors = [];

		$lines = preg_split('/$\R?^/m', $content);

		array_walk($lines, function(&$value, $i) { $value = trim($value); });

		$current = [];

		foreach ($lines as $line)
		{
			$line = trim($line);
			// Division
			if (strtoupper(substr($line, 0, 4)) === 'DIV:')
			{
				$name = trim(substr($line, 4));

				$colors = new stdClass();

				$colors->name = $name;
				$colors->colors = $current;

				array_push($this->colors, $colors);

				$current = [];
			}
			// Colors
			elseif (!empty($line) && substr($line, 0, 2) !== '//')
			{
				$values = preg_split('/\s+/', $line, 5);

				$name = (count($values) > 4) ? trim(substr($values[4], 2)) : '';

				$color = new Color($name);
				$color->R($values[0] + 0);
				$color->G($values[1] + 0);
				$color->B($values[2] + 0);
				$color->A($values[3] + 0);

				array_push($current, $color);
			}
		}

		// Put in the rest of them
		if (count($current) > 0)
		{
			$colors = new stdClass();

			$colors->name = 'Unknown';
			$colors->colors = $current;

			array_push($this->colors, $colors);
		}
	}

	public function Get()
	{
		return $this->colors;
	}
}

?>