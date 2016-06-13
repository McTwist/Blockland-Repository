<?php

namespace App\Repository\Blockland\Addon;

use App\Repository\Archive\ArchiveFile;

/*
 * FileRTBInfo
 * Handles the rtbinfo file
 * Return To Blockland
 */
class FileRTBInfo extends ArchiveFile
{
	private $id = '';
	private $icon = '';
	private $type = '';
	private $title = '';
	private $version = '';

	// It's constant, as that is how RTB handled it
	const NL = "\r\n";

	// Read the info
	public function Read($content)
	{
		// Split it into suitable pieces
		$lines = preg_split('/$\R?^/m', $content);

		foreach ($lines as $line)
		{
			// Id
			if (substr($line, 0, 3) == 'id:')
			{
				$this->id = trim(substr($line, 3));
			}
			// Icon
			elseif (substr($line, 0, 5) == 'icon:')
			{
				$this->icon = trim(substr($line, 5));
			}
			// Type
			elseif (substr($line, 0, 5) == 'type:')
			{
				$this->type = trim(substr($line, 5));
			}
			// Title
			elseif (substr($line, 0, 6) == 'title:')
			{
				$this->title = trim(substr($line, 6));
			}
			// Version
			elseif (substr($line, 0, 8) == 'version:')
			{
				$this->version = trim(substr($line, 8));
			}
		}
	}

	// Validate the info
	public function Validate()
	{
		return !empty($this->id)
			&& !empty($this->icon)
			&& !empty($this->type)
			&& !empty($this->title)
			&& !empty($this->version);
	}

	// Generate new description content
	public function Write()
	{
		// I can't let you do that
		if (!$this->Validate())
			return null;

		// Prepare the data
		$content = '';
		$content .= "id: {$this->id}".self::NL;
		$content .= "icon: {$this->icon}".self::NL;
		$content .= "type: {$this->type}".self::NL;
		$content .= "title: {$this->title}".self::NL;
		$content .= "version: {$this->version}".self::NL;

		return $content;
	}

	public function Id($value = null)
	{
		$id = $this->id;
		if ($value !== null)
			$this->id = $value;
		return $id;
	}

	public function Icon($value = null)
	{
		$icon = $this->icon;
		if ($value !== null)
			$this->icon = $value;
		return $icon;
	}

	public function Type($value = null)
	{
		$type = $this->type;
		if ($value !== null)
			$this->type = $value;
		return $type;
	}

	public function Title($value = null)
	{
		$title = $this->title;
		if ($value !== null)
			$this->title = $value;
		return $title;
	}

	public function Version($value = null)
	{
		$version = $this->version;
		if ($value !== null)
			$this->version = $value;
		return $version;
	}
}

?>