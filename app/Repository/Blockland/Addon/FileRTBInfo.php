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

	public function __construct($archive, $filename)
	{
		parent::__construct($archive, $filename);

		$this->AddAttribute('content', function()
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
		}, function($value)
		{
			// Split it into suitable pieces
			$lines = preg_split('/$\R?^/m', $value);

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
		});
		$this->AddAttribute('id', function() { return $this->id; }, function($value) { $this->id = $value; });
		$this->AddAttribute('icon', function() { return $this->icon; }, function($value) { $this->icon = $value; });
		$this->AddAttribute('type', function() { return $this->type; }, function($value) { $this->type = $value; });
		$this->AddAttribute('title', function() { return $this->title; }, function($value) { $this->title = $value; });
		$this->AddAttribute('version', function() { return $this->version; }, function($value) { $this->version = $value; });
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
}

?>
