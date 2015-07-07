<?php
/*
 * File
 * Handles an add-on file and its content
 * Supports Greek2me's Updater add-on for easier updating
 */

class File
{
	private $archive = null;

	private $type = null;
	private $name = null;

	// description.txt
	private $title = null;
	private $author = null;
	private $description = null;

	// namecheck.txt
	private $namecheck = null;

	// version.txt
	private $version = '0.0';
	private $channel = '*';
	private $repositories = [];
	private $formats = [];
	private $id = null;

	public function __construct($file)
	{
		// Read archive
		$this->archive = new ZipArchive();
		if ($this->archive->open($file) !== true)
		{
			$this->archive = null;
			return;
		}

		// Parse filename
		$underscore = strpos($file, '_');
		if ($underscore !== false)
		{
			$this->type = substr($file, 0, $underscore);
			list($this->name) = explode('.', substr($file, $underscore + 1));
		}
		else
		{
			list($this->name) = explode('.', $file);
		}


		// Read in default information if it exists
		$this->ReadVersion();
		$this->ReadDescription();
		$this->ReadNamecheck();
	}

	public function IsOpen()
	{
		return $this->archive !== null;
	}

	public function Type($lower = false)
	{
		return $lower ? strtolower($this->type) : $this->type;
	}

	public function Name()
	{
		return $this->name;
	}

	public function Title($value = null)
	{
		$title = $this->title;
		if ($value !== null)
			$this->title = $value;
		return $title;
	}

	public function Author($value = null)
	{
		$author = $this->author;
		if ($value !== null)
			$this->author = $value;
		return $author;
	}

	public function Description($value = null)
	{
		$description = $this->description;
		if ($value !== null)
			$this->description = $value;
		return $description;
	}

	public function Namecheck()
	{
		return $this->namecheck;
	}

	public function Version($value = null)
	{
		$version = $this->version;
		if ($value !== null)
			$this->version = $value;
		return $version;
	}

	public function Channel($value = null)
	{
		$channel = $this->channel;
		if ($value !== null)
			$this->channel = $value;
		return $channel;
	}

	public function Repositories(array $value = null)
	{
		$repositories = $this->repositories;
		if ($value !== null)
			$this->repositories = $value;
		return $repositories;
	}

	public function Formats(array $value = null)
	{
		$formats = $this->formats;
		if ($value !== null)
			$this->formats = $value;
		return $formats;
	}

	public function Id($value = null)
	{
		$id = $this->id;
		if ($value !== null)
			$this->id = $value;
		return $id;
	}

	// Validates file to contain the required data
	public function Validate()
	{
		return $this->ValidateDescription() &&
			$this->ValidateNamecheck() &&
			$this->ValidateVersion() &&
			$this->ValidateScripts();
	}

	public function ValidateScripts()
	{
		// TODO: Go through all scripts and verify that they are correct
		return true;
	}

	// Validate internal description file
	public function ValidateDescription()
	{
		// Check for file that to check for
		if (!$this->HaveFile('description.txt'))
			return false;

		// Read content
		$content = $this->ReadFile('description.txt');

		// Split it into suitable pieces
		$lines = preg_split('/$\R?^/m', $content, 3);

		if (substr($lines[0], 0, 6) !== 'Title:')
			return false;
		if (substr($lines[1], 0, 7) !== 'Author:')
			return false;

		return true;
	}

	// Read the file
	public function ReadDescription()
	{
		// Check for file that to check for
		if (!$this->HaveFile('description.txt'))
			return;

		// Read content
		$content = $this->ReadFile('description.txt');

		// Split it into suitable pieces
		$lines = preg_split('/$\R?^/m', $content, 3);

		// Easy parser
		$this->title = trim(substr($lines[0], 6));
		$this->author = trim(substr($lines[1], 7));
		$this->description = $lines[2];
	}

	// Validate internal namecheck file
	public function ValidateNamecheck()
	{
		// Check for file that to check for
		if (!$this->HaveFile('namecheck.txt'))
			return true;

		$namecheck = $this->ReadFile('namecheck.txt');

		if ($namecheck !== basename($this->archive->filename, '.zip'))
			return false;

		return true;
	}

	// Read the file
	public function ReadNamecheck()
	{
		// Check for file that to check for
		if (!$this->HaveFile('namecheck.txt'))
			return;

		$this->namecheck = $this->ReadFile('namecheck.txt');
	}

	// Validate the file types in the archive
	public function ValidateFileTypes()
	{
		for ($i = 0; $i < $numFiles; ++$i)
		{
			$stat = $this->archive->statIndex($i);

			// TODO: Look for files from a whitelist
		}
	}

	public function IsClient()
	{
		return $this->HaveFile('client.cs');
	}

	public function IsServer()
	{
		return $this->HaveFile('server.cs');
	}

	public function IsGameMode()
	{
		return $this->HaveFile('gamemode.txt');
	}

	public function HaveColorset()
	{
		return $this->HaveFile('colorset.txt');
	}

	public function HasBricks()
	{
		for ($i = 0; $i < $this->archive->numFiles; $i++)
		{
			$stat = $this->archive->statIndex($i);
			if (end(explode('.', $stat['name'])) === 'blb')
				return true;
		}
		return false;
	}

	// Generate a description.txt file
	public function GenerateDescription($overwrite = false)
	{
		if (!$overwrite && $this->HaveFile('description.txt'))
			return;

		// Make sure you write correct data
		if (empty($this->title) || empty($this->author) || empty($this->description))
			return;

		// Prepare the data
		$content  = "Title: {$this->title}\n";
		$content .= "Author: {$this->author}\n";
		$content .= "{$this->description}";

		// Save it
		$this->archive->addFromString('description.txt', $content);
	}

	// Generate a namecheck.txt file
	public function GenerateNamecheck($overwrite = false)
	{
		if (!$overwrite && $this->HaveFile('namecheck.txt'))
			return;

		// Ignore everything and just add the filename as it should be
		$this->archive->addFromString('namecheck.txt', basename($this->archive->filename, '.zip'));
	}

	// Get out all those pesky files that somehow get into every other add-on
	public function Cleanup()
	{
		$this->RemoveFile('Thumbs.db'); // Windows thumbnails
		$this->RemoveFile('.DS_Store'); // Mac folder attributes
	}

	private function RemoveFile($file)
	{
		$found = false;
		while (($index = $this->archive->locateName($file, ZipArchive::FL_NODIR | ZipArchive::FL_NOCASE)) !== false)
			$found |= $this->archive->deleteIndex($index);
		return $found;
	}

	private function HaveFile($file)
	{
		return $this->archive->locateName($file, ZipArchive::FL_NOCASE) !== false;
	}

	private function ReadFile($file)
	{
		return $this->archive->getFromName($file, 0, ZipArchive::FL_NOCASE);
	}

	// Greek2me's Updater
	// Validate version.txt
	public function ValidateVersion()
	{
		// Check for file that to check for
		if (!$this->HaveFile('version.txt'))
			return false;

		// Get content
		$content = $this->ReadFile('version.txt');

		// Split up the lines into an array
		$lines = preg_split('/$\R?^/m', $content);

		// Split up the fields internally into arrays
		array_walk($lines, function(&$value, $i) { $value = preg_split('/\s+/', trim($value)); });

		$repositories = [];
		$formats = [];
		foreach ($lines as $line)
		{
			// Avoid problem
			if (count($line) < 2)
				continue;
			switch ($line[0])
			{
			case 'version': case 'version:': case 'vers':
				$version = $line[1];
				break;
			case 'channel': case 'channel:': case 'chan':
				$channel = $line[1];
				break;
			case 'repository': case 'repository:': case 'repo':
				array_push($repositories, $line[1]);
				if (count($line) > 2)
					array_push($formats, $line[2]);
				break;
			case 'format': case 'format:': case 'form':
				if (count($formats) == 0)
					array_push($formats, $line[1]);
				break;
			case 'id': case 'id:':
				$id = (int)$line[1];
				break;
			}
		}

		// And check if it's valid
		return !empty($version) && !empty($channel) && !empty($repositories);
	}

	// Read version.txt
	public function ReadVersion()
	{
		// Check for file that to check for
		if (!$this->HaveFile('version.txt'))
			return;

		// Get content
		$content = $this->ReadFile('version.txt');

		// Split up the lines into an array
		$lines = preg_split('/$\R?^/m', $content);

		// Split up the fields internally into arrays
		array_walk($lines, function(&$value, $i) { $value = preg_split('/\s+/', trim($value)); });

		foreach ($lines as $line)
		{
			// Avoid problem
			if (count($line) < 2)
				continue;
			// Note: Values are taken directly from the Updater
			switch ($line[0])
			{
			case 'version': case 'version:': case 'vers':
				$this->version = $line[1];
				break;
			case 'channel': case 'channel:': case 'chan':
				$this->channel = $line[1];
				break;
			case 'repository': case 'repository:': case 'repo':
				array_push($this->repositories, $line[1]);
				if (count($line) > 2)
					array_push($this->formats, $line[2]);
				break;
			case 'format': case 'format:': case 'form':
				if (count($this->formats) == 0)
					array_push($this->formats, $line[1]);
				break;
			case 'id': case 'id:':
				$this->id = (int)$line[1];
				break;
			}
		}
	}

	// Generate a version file
	public function GenerateVersion($overwrite = false)
	{
		if (!$overwrite && $this->HaveFile('version.txt'))
			return;

		$count = count($this->repositories);

		// Check a couple of restrainments
		if (empty($this->version) || empty($this->channel) || 
			$count == 0 || $count != count($this->formats))
			return;

		// Prepare data
		$content  = "version {$this->version}\n";
		$content .= "channel {$this->channel}\n";
		for ($i = 0; $i < $count; ++$i)
			$content .= "repository {$this->repositories[$i]} {$this->formats[$i]}\n";
		if ($this->id !== null)
			$content .= "id {$this->id}";

		// Save it
		$this->archive->addFromString('version.txt', $content);
	}
}

?>