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

	private $filetype_count = [];

	// description.txt
	private $description = null;

	// namecheck.txt
	private $namecheck = null;

	// version.txt
	private $version = null;

	// rtbInfo.txt
	private $rtbInfo = null;

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

		$this->description = new FileDescription();
		$this->version = new FileVersion();
		$this->rtbInfo = new FileRTBInfo();

		// Read in default information if it exists
		$this->ReadVersion();
		$this->ReadDescription();
		$this->ReadNamecheck();
		$this->ReadRTB();

		// Count all file types
		for ($i = 0; $i < $this->archive->numFiles; $i++)
		{
			$stat = $this->archive->statIndex($i);
			$ext = strtolower(end(explode('.', $stat['name'])));

			if (!isset($this->filetype_count[$ext]))
				$this->filetype_count[$ext] = 0;

			++$this->filetype_count[$ext];
		}
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
		$title = $this->description->Title($value);
		if ($title === null)
			$title = $this->rtbInfo->Title($value);
		if ($title === null)
			$title = $this->Name();
		return $title;
	}

	public function Authors($value = null)
	{
		return $this->description->Authors($value);
	}

	public function Description($value = null)
	{
		return $this->description->Description($value);
	}

	public function Namecheck()
	{
		return isset($this->namecheck) ? $this->namecheck->Get() : '';
	}

	public function Version($value = null)
	{
		return $this->version->Version($value);
	}

	public function Channel($value = null)
	{
		return $this->version->Channel($value);
	}

	public function AddRepository($url, $format = null, $id = null)
	{
		return $this->version->AddRepository($url, $format, $id);
	}

	public function SetRepository($url, $format = null, $id = null)
	{
		return $this->version->SetRepository($url, $format, $id);
	}

	public function Repositories()
	{
		//return $this->repositories;
		return $this->version->Repositories();
	}

	// Direct access
	public function &GetNamecheck()
	{
		return $this->namecheck;
	}

	public function &GetDescription()
	{
		return $this->description;
	}

	public function &GetVersion()
	{
		return $this->version;
	}

	public function &GetRTBInfo()
	{
		return $this->rtbInfo;
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
		return $this->description->Validate();
	}

	// Read the file
	public function ReadDescription()
	{
		if ($this->HaveFile('description.txt'))
			$this->description->Read($this->ReadFile('description.txt'));
	}

	// Generate a description.txt file
	public function GenerateDescription($overwrite = false)
	{
		if (!$overwrite && $this->HaveFile('description.txt'))
			return;

		$content = $this->description->Generate();

		// Save it
		if (!empty($content))
			$this->archive->addFromString('description.txt', $content);
	}

	// Validate internal namecheck file
	public function ValidateNamecheck()
	{
		// Check for file that to check for
		if (!$this->HaveFile('namecheck.txt'))
			return true;

		return $this->namecheck->Validate();
	}

	// Read the file
	public function ReadNamecheck()
	{
		// Check for file that to check for
		$namecheck = ($this->HaveFile('namecheck.txt')) ? $this->ReadFile('namecheck.txt') : '';
		
		$this->namecheck = new FileNamecheck($this->archive->filename, $namecheck);
	}

	// Generate a namecheck.txt file
	public function GenerateNamecheck($overwrite = false)
	{
		if (!$overwrite && $this->HaveFile('namecheck.txt'))
			return;

		$this->archive->addFromString('namecheck.txt', $this->namecheck->Generate());
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

	// A small check to see if people have included code, but no file to execute it from
	public function CanExecute()
	{
		return !$this->IsClient() && !$this->IsServer() && $this->HasFileType('cs');
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
		return $this->HasFileType('blb'); // Brick format
	}

	public function HasMusic()
	{
		return $this->HasFileType('ogg');
	}

	public function HasSound()
	{
		return $this->HasFileType('wav');
	}

	public function HasImages()
	{
		return $this->HasFileType('png')
			|| $this->HasFileType('jpg');
	}

	public function HasModels()
	{
		return $this->HasFileType('dts'); // Normal models
	}

	public function HasAnimations()
	{
		return $this->HasFileType('dsq');
	}

	public function HasSave()
	{
		return $this->HasFileType('bls');
	}

	public function HasAtmosphere()
	{
		return $this->HasFileType('atmosphere');
	}

	public function HasSkyboxTexture()
	{
		return $this->HasFileType('dml');
	}

	// Deprecated file types

	public function HasDeprecatedFiles()
	{
		return $this->HasFileType('dif') // Models for interiors
			|| $this->HasTerrain()
			|| $this->HasLight()
			|| $this->HasMission();
	}

	// Terrain files. dts may be used instead
	public function HasTerrain()
	{
		return $this->HasFileType('ter');
	}
	
	// Light file
	public function HasLight()
	{
		return $this->HasFileType('ml');
	}

	// Mission file, use gamemode.txt, atmosphere and/or dml
	public function HasMission()
	{
		return $this->HasFileType('mis');
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

	private function HasFileType($ext)
	{
		return isset($this->filetype_count[strtolower($ext)]);
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

		return $this->version->Validate();
	}

	// Read version.txt
	public function ReadVersion()
	{
		// Old version.txt format
		if ($this->HaveFile('version.txt'))
		{
			$this->version->Read($this->ReadFile('version.txt'), false);
		}
		// New JSON format
		elseif ($this->HaveFile('version.json'))
		{
			$this->version->Read($this->ReadFile('version.json'), true);
		}
	}

	// Generate a version file
	// Pretty only works with JSON
	public function GenerateVersion($overwrite = false, $json = true, $pretty = true)
	{
		$version_txt = $this->HaveFile('version.txt');
		$version_json = $this->HaveFile('version.json');

		if (!$this->version->Validate())
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

		$content = $this->version->Generate($json, $pretty);
		if (!empty($content))
			$this->archive->addFromString($json ? 'version.json' : 'version.txt', $content);
	}

	// RTB info
	// Validate rtbInfo.txt
	public function ValidateRTB()
	{
		return $this->rtbInfo->Validate();
	}

	// Read rtbInfo.txt
	public function ReadRTB()
	{
		if ($this->HaveFile('rtbInfo.txt'))
			$this->rtbInfo->Read($this->ReadFile('rtbInfo.txt'));
	}

	// Generate a rtbInfo file
	public function GenerateRTB($overwrite = false)
	{
		if (!$overwrite && $this->HaveFile('rtbInfo.txt'))
			return;

		$content = $this->rtbInfo->Generate();

		// Save it
		if (!empty($content))
			$this->archive->addFromString('rtbInfo.txt', $content);
	}

	// Check if RTB exists
	public function HaveRTB()
	{
		return $this->HaveFile('rtbInfo.txt');
	}

	// Remove RTB
	public function RemoveRTB()
	{
		return $this->RemoveFile('rtbInfo.txt');
	}
}

/*
 * FileNamecheck
 * Handles the name checking
 */
class FileNamecheck
{
	private $filename = '';
	private $namecheck = '';

	public function __construct($filename, $namecheck = '')
	{
		$this->filename = basename($filename, '.zip');
		$this->namecheck = $namecheck;
	}

	// Validate the name
	public function Validate()
	{
		return $this->namecheck === $this->filename;
	}

	// Generate a new namecheck
	public function Generate()
	{
		// Ignore everything and just add the filename as it should be
		return $this->namecheck = $this->filename;
	}

	public function Get()
	{
		return $this->namecheck;
	}
}

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

/*
 * FileNamecheck
 * Handles the version file
 * Greek2me's Updater
 */
class FileVersion
{
	private $version = '0.0';
	private $channel = '*';
	private $repositories = [];

	const NL = File::NL;

	// Read version
	public function Read($content, $json)
	{
		$this->repositories = [];

		// Old version.txt format
		if (!$json)
		{
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
		else
		{
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

	// Validate version
	public function Validate()
	{
		return !empty($this->version)
			&& !empty($this->channel)
			&& count($this->repositories) > 0;
	}

	// Generate a version file
	// Pretty only works with JSON
	public function Generate($json = true, $pretty = true)
	{
		// Check a couple of restrainments
		if (empty($this->version) || empty($this->channel) || count($this->repositories) == 0)
			return '';

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
		}

		return $content;
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
			return $this->SetRepository($url, $format, $id);

		$repo = new stdClass();
		$repo->format = $format;
		$repo->id = $id;
		$this->repositories[$url] = $repo;
	}

	public function SetRepository($url, $format = null, $id = null)
	{
		if (!isset($this->repositories[$url]))
			return $this->AddRepository($url, $format, $id);

		if (isset($format))
			$this->repositories[$url]->format = $format;
		if (isset($id))
			$this->repositories[$url]->id = $id;
	}

	public function Repositories()
	{
		return $this->repositories;
	}
}

/*
 * FileRTBInfo
 * Handles the rtbinfo file
 * Return To Blockland
 */
class FileRTBInfo
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
	public function Generate()
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