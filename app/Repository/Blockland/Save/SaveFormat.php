<?php

namespace App\Repository\Blockland\Save;

use App\Repository\Blockland\Colorset\Color;

/*
 * SaveFormat
 * Save format in Blockland (bls files)
 *
 * Encoding is automatically converted from "Western (Windows 1252)" to "UTF-8"
 *
 * Due to memory limits, this was made as an iterator, reducing memory footprint significantly
 * Keep in mind that this almost doubles the time it takes to load a file
 * Reading chunks(8-64KiB) and parse them locally could be faster and a thing to look into in the future
 *
 * SplFileObject has been tested to be slower (But could be faster in some circumstances)
 *
 * Generators have also been tested and was a bit slower
 */
class SaveFormat implements \Iterator
{
	private $colors = [];

	// State variables
	private $linecount = 0;
	private $brick = null;
	private $next_brick = null;
	private $file = null;
	private $brick_start = 0;
	private $brick_counter = -1;

	const NL = "\r\n";

	public function __construct($file)
	{
		$this->file = fopen($file, 'r');
		$this->ReadHeader();
		// Reads the first brick
		$this->rewind();
	}

	public function __destruct()
	{
		fclose($this->file);
	}

	// Read one line
	private function ReadLine()
	{
		$this->ParseLine($this->Read());
	}

	// Parse one line
	private function ParseLine($line)
	{
		$line = trim($line, self::NL);
		// Nothing to handle
		if (empty($line))
			return;
		// Color
		if ($this->linecount === 0 && count($color = preg_split('/\\s?(\\d+\\.\\d+)\\s?/', $line, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)) === 4)
		{
			$color = array_map('floatval', $color);
			array_push($this->colors, new Color($color[0], $color[1], $color[2], $color[3]));
		}
		// Line count
		elseif ($this->linecount === 0 && substr($line, 0, 9) === 'Linecount')
		{
			list(, $this->linecount) = explode(' ', $line);
		}
		// Attribute
		elseif ($this->brick !== null && substr($line, 0, 2) === '+-')
		{
			list($name, $values) = preg_split('/\s+/', $line, 2);
			$this->ParseAttribute($name, $values);
		}
		// Brick
		else
		{
			$values = [];
			list($name, $rest) = self::GetString($line);
			$values[] = $name;
			$values = array_merge($values, explode(' ', $rest));

			$this->brick = new SaveFormatBrick();
			$this->ReadBrick($values);
		}
	}

	// Read in one brick
	private function ReadBrick($values)
	{
		$brick = &$this->brick;
		list(
			$brick->name,
			$brick->x, $brick->y, $brick->z,
			$brick->rotation_id,
			$brick->baseplate,
			$brick->color,
			$brick->print_id,
			$brick->color_fx_id,
			$brick->shape_fx_id,
			$brick->raycasting,
			$brick->collision,
			$brick->rendering) = $values;
		// Special color handling
		$brick->color = $this->colors[$brick->color];
	}

	// Parse an attribute
	private function ParseAttribute($name, $values)
	{
		$brick = &$this->brick;
		$name = substr($name, 2);
		switch ($name)
		{
		case 'ITEM':
			list($item, $values) = self::GetString($values);
			list($list, $direction, $respawn_time) = preg_split('/\s+/', $values);
			$brick->item['item'] = $item;
			$brick->item['list'] = $list;
			$brick->item['direction'] = $direction;
			$brick->item['respawn_time'] = $respawn_time;
			break;
		case 'LIGHT':
			list($light) = self::GetString($values);
			$brick->light['light'] = $light;
			break;
		case 'EMITTER':
			list($emitter, $direction) = self::GetString($values);
			$brick->emitter['emitter'] = $emitter;
			$brick->emitter['direction'] = $direction;
			break;
		case 'AUDIOEMITTER':
			list($audioemitter) = self::GetString($values);
			$brick->audioemitter['audioemitter'] = $audioemitter;
			break;
		case 'VEHICLE':
			list($vehicle, $color) = self::GetString($values);
			$brick->vehicle['vehicle'] = $vehicle;
			$brick->vehicle['color'] = $this->colors[$color];
			break;
		case 'OWNER':
			$bl_id = $values;
			$brick->bl_id = $bl_id;
			break;
		case 'EVENT':
			list($number, $enabled, $input, $delay, $target, $target_name, $event_output, $params) = explode("\t", $values, 8);
			$params = explode("\t", $params);

			$event = [];
			$event['number'] = $number;
			$event['enabled'] = $enabled;
			$event['input'] = $input;
			$event['delay'] = $delay;
			$event['target'] = $target;
			$event['target_name'] = $target_name;
			$event['event_output'] = $event_output;
			$event['params'] = $params;

			$brick->events[] = $event;
			break;
		case 'NTOBJECTNAME':
			$name = $values;
			$brick->ntobjectname['name'] = bl_convert_encoding($name);
			break;
		}
	}

	// Get string from line
	private static function GetString($str)
	{
		$list = explode('" ', $str, 2);
		$list[0] = bl_convert_encoding($list[0]);
		return $list;
	}

	// Read the headers
	private function ReadHeader()
	{
		fseek($this->file, 0);
		// Skip the first three lines
		fgets($this->file);
		fgets($this->file);
		fgets($this->file);
		$this->linecount = 0;

		while ($this->linecount == 0 && !feof($this->file))
		{
			$this->ReadLine();
		}

		$this->brick_start = ftell($this->file);
	}

	// Get next brick
	private function NextBrick()
	{
		$brick = null;
		$this->brick = $this->next_brick;
		$this->next_brick = null;

		$this->brick_counter++;

		while (!feof($this->file))
		{
			$this->ReadLine();

			if ($brick === null)
			{
				$brick = $this->brick;
			}
			// Different brick, so done
			elseif ($brick != $this->brick)
			{
				// Revert it
				$this->next_brick = $this->brick;
				$this->brick = $brick;
				break;
			}
		}
	}

	// Read one line
	private function Read()
	{
		return fgets($this->file);
	}

	// Get line count
	public function Linecount()
	{
		return $this->linecount;
	}

	// Iterator implementation
	public function rewind()
	{
		fseek($this->file, $this->brick_start);
		$this->brick_counter = -1;
		$this->NextBrick();
	}

	public function current()
	{
		return $this->brick;
	}

	public function key()
	{
		return $this->brick_counter;
	}

	public function next()
	{
		$this->NextBrick();
		return $this->brick;
	}

	public function valid()
	{
		return $this->brick !== null;
	}
}

?>