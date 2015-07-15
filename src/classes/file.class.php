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
	private $authors = [];
	private $description = null;

	// namecheck.txt
	private $namecheck = null;

	// version.txt
	private $version = '0.0';
	private $channel = '*';
	private $repositories = [];

	const NL = "\r\n";

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

	public function AddRepository($url, $format = null, $id = null)
	{
		if (isset($this->repositories[$url]))
			return SetRepository($url, $format, $id);

		$repo = new stdClass();
		$repo->format = $format;
		$repo->id = $id;
		$this->repositories[$url] = $repo;
	}

	public function SetRepository($url, $format = null, $id = null)
	{
		if (!isset($this->repositories[$url]))
			return AddRepository($url, $format, $id);

		if (isset($format))
			$this->repositories[$url]->format = $format;
		if (isset($id))
			$this->repositories[$url]->id = $id;
	}

	public function Repositories()
	{
		return $this->repositories;
	}

	// Validates file to contain the required data
	public function Validate()
	{
		return $this->ValidateDescription()
			&& $this->ValidateNamecheck()
			&& $this->ValidateVersion()
			&& $this->ValidateScripts();
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
		$authors = preg_split('/(\,|\;)/', substr($lines[1], 7));
		array_walk($authors, function(&$value, $i) { $value = trim($value); });
		$this->authors = $authors;
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

	// Validate gmae mode files
	public function ValidateGameMode()
	{
		return $this->IsGameMode()
			&& $this->HaveColorset()
			&& $this->HaveFile('description.txt')
			&& $this->HaveFile('save.bls')
			&& $this->HaveFile('preview.jpg')
			&& $this->HaveFile('thumb.jpg');
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

		$authors = implode(', ', $this->authors);

		// Prepare the data
		$content = '';
		if (!empty($this->title))
			$content .= "Title: {$this->title}".self::NL;
		if (!empty($authors))
			$content .= "Author: {$authors}".self::NL;
		if (!empty($this->description))
			$content .= "{$this->description}";

		// Save it
		if (!empty($content))
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
		$version_txt = $this->HaveFile('version.txt');
		$version_json = $this->HaveFile('version.json');

		// Only allow one file
		if ($version_txt && $version_json)
			return false;

		// Old version.txt format
		if ($version_txt)
		{
			// Get content
			$content = $this->ReadFile('version.txt');

			// Split up the lines into an array
			$lines = preg_split('/$\R?^/m', $content);

			// Split up the fields internally into arrays
			array_walk($lines, function(&$value, $i) { $value = preg_split('/\s+/', trim($value)); });

			$repositories = [];
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
					break;
				}
			}
		}
		// New JSON format
		elseif ($version_json)
		{
			// Get content
			$content = $this->ReadFile('version.json');

			$data = json_decode($content);

			// Could not decode
			if ($data === null)
				return false;

			if (!isset($data->version))
				return false;
			if (!isset($data->channel))
				return false;

			if (isset($data->repositories))
			{
				foreach ($data->repositories as $repository)
				{
					if (isset($repository->url))
						return true;
				}
			}

			return false;
		}


		// And check if it's valid
		return !empty($version) && !empty($channel) && !empty($repositories);
	}

	// Read version.txt
	public function ReadVersion()
	{
		// Old version.txt format
		if ($this->HaveFile('version.txt'))
		{
			$this->repositories = [];

			// Get content
			$content = $this->ReadFile('version.txt');

			// Split up the lines into an array
			$lines = preg_split('/$\R?^/m', $content);

			// Split up the fields internally into arrays
			array_walk($lines, function(&$value, $i) { $value = preg_split('/\s+/', trim($value)); });

			// Default values
			$format = null;
			$id = null;
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
					$this->AddRepository(
						$line[1],
						count($line) > 2 ? $line[2] : null,
						count($line) > 3 ? $line[3] : null);
					break;
				case 'format': case 'format:': case 'form':
					$format = $line[1];
					break;
				case 'id': case 'id:':
					$id = (int)$line[1];
					break;
				}
			}

			// Set defaults
			foreach ($this->repositories as &$repo)
			{
				if (!isset($repo->format))
					$repo->format = $format;
				if (!isset($repo->id))
					$repo->id = $id;
			}
		}
		// New JSON format
		elseif ($this->HaveFile('version.json'))
		{
			// Get content
			$content = $this->ReadFile('version.json');

			$data = json_decode($content);

			// Could not decode
			if ($data === null)
				return;

			$this->repositories = [];

			if (isset($data->version))
				$this->version = $data->version;
			if (isset($data->channel))
				$this->channel = $data->channel;

			if (isset($data->repositories))
			{
				foreach ($data->repositories as $repository)
				{
					if (isset($repository->url))
					{
						$this->AddRepository(
							$repository->url,
							isset($repository->format) ? $repository->format : null,
							isset($repository->id) ? $repository->id : null);
					}
				}
			}
		}
	}

	// Generate a version file
	// Pretty only works with JSON
	public function GenerateVersion($overwrite = false, $json = true, $pretty = true)
	{
		$version_txt = $this->HaveFile('version.txt');
		$version_json = $this->HaveFile('version.json');

		// Check a couple of restrainments
		if (empty($this->version) || empty($this->channel) || count($this->repositories) == 0)
			return;

		// Remove old files if overwriting
		// This will also fix the archive if it got both files
		if ($overwrite)
		{
			if ($version_txt)
				$this->RemoveFile('version.txt');
			if ($version_json)
				$this->RemoveFile('version.json');
		}
		// Have it already, so don't do anything
		elseif ($version_txt || $version_json)
		{
			return;
		}

		// Old version
		if (!$json)
		{
			// Prepare data
			$content  = "version {$this->version}".self::NL;
			$content .= "channel {$this->channel}".self::NL;
			foreach ($this->repositories as $url => $repo)
			{
				$content .= "repository {$url} {$url}";
				if (isset($repo->format))
					$content .= " {$repo->format}";
				if (isset($repo->id))
					$content .= " {$repo->id}";
				$content .= self::NL;
			}

			// Save it
			$this->archive->addFromString('version.txt', $content);
		}
		// New format
		else
		{
			// Prepare data
			$data = new stdClass();
			$data->version = $this->version;
			$data->channel = $this->channel;
			$data->repositories = [];
			foreach ($this->repositories as $url => $repo)
			{
				// Note: New object is required to sort the data
				$rep = new stdClass();
				$rep->url = $url;
				if (isset($repo->format))
					$rep->format = $repo->format;
				if (isset($repo->id))
					$rep->id = $repo->id;
				array_push($data->repositories, $rep);
			}

			// Generate content
			$content = json_encode($data, ($pretty ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES);

			// Save it
			$this->archive->addFromString('version.json', $content);
		}
	}
}

?>