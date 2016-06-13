<?php

namespace App\Repository\Addon;

use App\Repository\Archive\ArchiveFile;

/*
 * FileDescription
 * Handles the description content
 */
class FileDescription extends ArchiveFile
{
	private $title = null;
	private $authorsRaw = null;
	private $authors = [];
	private $description = null;

	const NL = File::NL;

	// Read the description information
	public function Read($content)
	{
		// Split it into suitable pieces
		$lines = preg_split('/$\R?^/m', $content);

		foreach ($lines as $line)
		{
			$lower = strtolower($line);
			// Title
			if (substr($lower, 0, 6) == 'title:')
			{
				$this->title = trim(substr($line, 6));
			}
			// Author
			elseif (substr($lower, 0, 7) == 'author:')
			{
				$this->AuthorsRaw(substr($line, 7));
			}
			// Description
			else
			{
				$this->description .= $line;
			}
		}
	}

	// Validate the description
	public function Validate()
	{
		return !empty($this->title) && !empty($this->authors) && !empty($this->description);
	}

	// Generate new description content
	public function Write()
	{
		// Prepare the data
		$content = '';
		if (!empty($this->title))
			$content .= "Title: {$this->title}".self::NL;
		if (!empty($this->authorsRaw))
			$content .= "Author: {$this->authorsRaw}".self::NL;
		if (!empty($this->description))
			$content .= "{$this->description}";

		return $content;
	}

	public function Title($value = null)
	{
		$title = $this->title;
		if (isset($value))
			$this->title = $value;
		return $title;
	}

	public function Authors(array $value = null)
	{
		$authors = $this->authors;
		if (isset($value))
		{
			$this->authors = $value;
			// TODO: Maybe determine the outcome depending on input. Will make cleanup worthwile
			$this->authorsRaw = implode(', ', $this->authors);
		}
		return $authors;
	}

	public function AuthorsRaw($value = null)
	{
		$authorsRaw = $this->authorsRaw;
		if (isset($value))
		{
			$this->authorsRaw = $value;
			$authors = preg_split('/(\,|\;| and |\&)/i', $value);
			array_walk($authors, function(&$value, $i) { $value = trim($value); });
			$this->authors = $authors;
		}
		return $authorsRaw;
	}

	public function Description($value = null)
	{
		$description = $this->description;
		if (isset($value))
			$this->description = $value;
		return $description;
	}
}

?>