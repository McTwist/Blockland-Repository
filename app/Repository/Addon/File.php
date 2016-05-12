<?php

namespace App\Repository\Addon;

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
		$this->archive = new \ZipArchive();
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
			$values = explode('.', $stat['name']);
			$ext = strtolower(end($values));

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
		$this->RemoveFile('.svn'); // SVN
		$this->RemoveFile('.git'); // GIT
		$this->RemoveFile('.gitignore'); // GIT ignore
	}

	private function RemoveFile($file)
	{
		$found = false;
		while (($index = $this->archive->locateName($file, \ZipArchive::FL_NODIR | \ZipArchive::FL_NOCASE)) !== false)
			$found |= $this->archive->deleteIndex($index);
		return $found;
	}

	private function HaveFile($file)
	{
		return $this->archive->locateName($file, \ZipArchive::FL_NOCASE) !== false;
	}

	private function HasFileType($ext)
	{
		return isset($this->filetype_count[strtolower($ext)]);
	}

	private function ReadFile($file)
	{
		return $this->archive->getFromName($file, 0, \ZipArchive::FL_NOCASE);
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

?>