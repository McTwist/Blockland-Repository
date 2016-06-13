<?php

namespace App\Repository\Addon;

use App\Repository\Archive\Archive;

/*
 * File
 * Handles an add-on file and its content
 * Supports Greek2me's Updater add-on for easier updating
 */

class File extends Archive
{
	private $type = null;
	private $name = null;

	// description.txt
	private $description = null;

	// namecheck.txt
	private $namecheck = null;

	// version.txt
	private $version = null;

	// rtbInfo.txt
	private $rtbInfo = null;

	public function __construct($file)
	{
		parent::__construct($file);

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

		$this->AddFileReader('namecheck.txt', FileNamecheck::class);
		$this->AddFileReader('description.txt', FileDescription::class);
		$this->AddFileReader(['version.txt', 'version.json'], FileVersion::class);
		$this->AddFileReader('rtbinfo.txt', FileRTBInfo::class);

		$this->namecheck = $this->GetNamecheck();
		$this->description = $this->GetDescription();
		$this->version = $this->GetVersion();
		$this->rtbInfo = $this->GetRTBInfo();
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
		return (!is_string($value)) ? $this->description->Authors($value) : $this->description->AuthorsRaw($value);
	}

	public function Description($value = null)
	{
		return $this->description->Description($value);
	}

	public function Namecheck()
	{
		return isset($this->namecheck) ? $this->namecheck->Namecheck() : '';
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

	protected function GetNamecheck()
	{
		return $this->GetFile('namecheck.txt');
	}

	protected function GetDescription()
	{
		return $this->GetFile('description.txt', true);
	}

	protected function GetVersion()
	{
		if ($this->HaveFile('version.json'))
		{
			return $this->GetFile('version.json', true);
		}
		if ($this->HaveFile('version.txt'))
		{
			return $this->GetFile('version.txt', true);
		}
	}

	protected function GetRTBInfo()
	{
		return $this->GetFile('rtbinfo.txt', true);
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

	// Generate a description.txt file
	public function GenerateDescription($overwrite = false)
	{
		if (!$overwrite && $this->HaveFile('description.txt'))
			return;

		$this->SetFile($this->description);
	}

	// Validate internal namecheck file
	public function ValidateNamecheck()
	{
		// Check for file that to check for
		if (!$this->HaveFile('namecheck.txt'))
			return true;

		return $this->namecheck->Validate();
	}

	// Generate a namecheck.txt file
	public function GenerateNamecheck($overwrite = false)
	{
		if (!$overwrite && $this->HaveFile('namecheck.txt'))
			return;

		$this->SetFile($this->namecheck);
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

	// Validate game mode files
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

		if ($version_txt)
			$this->version->ChangeFilename('version.txt');
		if ($version_json)
			$this->version->ChangeFilename('version.json');

		$this->SetFile($this->version);
	}

	// RTB info
	// Validate rtbInfo.txt
	public function ValidateRTB()
	{
		return $this->rtbInfo->Validate();
	}

	// Generate a rtbInfo file
	public function GenerateRTB($overwrite = false)
	{
		if (!$overwrite && $this->HaveFile('rtbInfo.txt'))
			return;

		$this->SetFile($this->rtbInfo);
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