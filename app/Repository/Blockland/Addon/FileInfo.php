<?php

namespace App\Repository\Blockland\Addon;

use App\Repository\Archive\ArchiveFile;

/*
 * FileInfo
 * Handles the informative content
 */
class FileInfo extends ArchiveFile
{
	private $title = null;
	private $authors_raw = null;
	private $authors = [];
	private $description = null;

	const NL = File::NL;

	public function __construct($archive, $filename)
	{
		parent::__construct($archive, $filename);

		$this->AddAttribute('content', function()
		{
			$content = '';

			if ($this->isCredits)
			{
				if (!empty($this->authors_raw))
					$content .= "{$this->authors_raw}";
			}
			else
			{
				if (!empty($this->title))
					$content .= "Title: {$this->title}".self::NL;
				if (!empty($this->authors_raw))
					$content .= "Author: {$this->authors_raw}".self::NL;
				if (!empty($this->description))
					$content .= "{$this->description}";
			}

			return $content;
		}, function($value)
		{
			$this->description = '';

			if ($this->isCredits)
			{
				$this->authorsRaw = $value;
			}
			else
			{
				// Split it into suitable pieces
				$lines = preg_split('/$\R?^/m', $value);
	
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
						$this->authorsRaw = substr($line, 7);
					}
					// Description
					else
					{
						$this->description .= $line;
					}
				}
			}
		});
		$this->AddAttribute('title', function() { return $this->title; }, function($value) { $this->title = $value; });
		$this->AddAttribute('authors', function() { return $this->authors; }, function($value)
		{
			$this->authors = $value;
			// TODO: Maybe determine the outcome depending on input. Will make cleanup worthwhile
			$this->authors_raw = implode(', ', $value);
		});
		$this->AddAttribute('authorsRaw', function() { return $this->authors_raw; }, function($value)
		{
			$this->authors_raw = trim($value);
			$authors = preg_split('/(\,|\;| and |\&)/i', $value);
			array_walk($authors, function(&$value, $i) { $value = trim($value); });
			$this->authors = $authors;
		});
		$this->AddAttribute('description', function() { return $this->description; }, function($value) { $this->description = $value; });

		$this->AddAttribute('isDescription', function() { return strtolower($this->filename) == 'description.txt'; }, null);
		$this->AddAttribute('isCredits', function() { return strtolower($this->filename) == 'credits.txt'; }, null);

		$this->AddAttribute('hasTitle', function() { return !empty($this->title); }, null);
		$this->AddAttribute('hasAuthors', function() { return !empty($this->authors); }, null);
		$this->AddAttribute('hasDescription', function() { return !empty($this->description); }, null);
	}

	// Validate the description
	public function Validate()
	{
		return ($this->isDescription && $this->hasTitle && $this->hasAuthors && $this->hasDescription) ||
			($this->isCredits && $this->hasAuthors);
	}
}

?>
