<?php

namespace App\Repository\Blockland\Save;

class SaveFormatBrick
{
	/*
	 * Values
	 */
	public $name = '';
	public $bl_id = -1;
	public $x = 0;
	public $y = 0;
	public $z = 0;
	public $rotation_id = 0;
	public $baseplate = false;
	public $color = null;
	public $print_id = '';
	public $color_fx_id = 0;
	public $shape_fx_id = 0;
	public $raycasting = true;
	public $collision = true;
	public $rendering = true;

	/*
	 * Attributes
	 */
	public $item = [];
	public $light = [];
	public $emitter = [];
	public $audioemitter = [];
	public $vehicle = [];
	public $owner = [];
	public $events = [];
	public $ntobjectname = [];
}

?>