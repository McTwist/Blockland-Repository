<?php

namespace App\Repository\Blockland\Colorset;

class Color
{
	private $name = '';
	private $r = 255;
	private $g = 255;
	private $b = 255;
	private $a = 255;

	public function __construct($name, $r = 255, $g = 255, $b = 255, $a = 255)
	{
		if (is_string($name))
		{
			$this->name = $name;
			$this->SetR($r);
			$this->SetG($g);
			$this->SetB($b);
			$this->SetA($a);
		}
		else
		{
			$this->SetR($name);
			$this->SetG($r);
			$this->SetB($g);
			$this->SetA($b);
		}
	}

	public function R()
	{
		return $this->r;
	}

	public function G()
	{
		return $this->g;
	}

	public function B()
	{
		return $this->b;
	}

	public function A()
	{
		return $this->a;
	}

	// Float representations
	public function RF()
	{
		return $this->r / 255;
	}

	public function GF()
	{
		return $this->g / 255;
	}

	public function BF()
	{
		return $this->b / 255;
	}

	public function AF()
	{
		return $this->a / 255;
	}

	public function SetR($r)
	{
		self::set_color($this->r, $r);
	}

	public function SetG($g)
	{
		self::set_color($this->g, $g);
	}

	public function SetB($b)
	{
		self::set_color($this->b, $b);
	}

	public function SetA($a)
	{
		self::set_color($this->a, $a);
	}

	public function Name()
	{
		return $this->name;
	}

	public function __toString()
	{
		$r = dechex($this->r);
		$g = dechex($this->g);
		$b = dechex($this->b);
		$color = (strlen($r) < 2?'0':'').$r;
		$color .= (strlen($g) < 2?'0':'').$g;
		$color .= (strlen($b) < 2?'0':'').$b;
		return "{$this->r} {$this->g} {$this->b} {$this->a}";
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

?>