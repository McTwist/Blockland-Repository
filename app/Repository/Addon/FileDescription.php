<?php

namespace App\Repository\Addon;

/*
 * FileDescription
 * Handles the description content
 */
class FileDescription
{
	private $title = null;
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
				$authors = preg_split('/(\,|\;| and |\&)/i', substr($line, 7));
				array_walk($authors, function(&$value, $i) { $value = trim($value); });
				$this->authors = $authors;
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
	public function Generate()
	{
		$authors = implode(', ', $this->authors);

		// Prepare the data
		$content = '';
		if (!empty($this->title))
			$content .= "Title: {$this->title}".self::NL;
		if (!empty($authors))
			$content .= "Author: {$authors}".self::NL;
		if (!empty($this->description))
			$content .= "{$this->description}";

		return $content;
	}

	public function Title($value = null)
	{
		$title = $this->title;
		if ($value !== null)
			$this->title = $value;
		return $title;
	}

	public function Authors($value = null)
	{
		$authors = $this->authors;
		if ($value !== null)
			$this->authors = (is_array($value)) ? $value : array($value);
		return $authors;
	}

	public function Description($value = null)
	{
		$description = $this->description;
		if ($value !== null)
			$this->description = $value;
		return $description;
	}
}

?>