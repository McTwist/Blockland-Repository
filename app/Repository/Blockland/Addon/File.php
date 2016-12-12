<?php

namespace App\Repository\Blockland\Addon;

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

	private $type_check = [];
	private $validation_test = [];

	public function __construct($file)
	{
		parent::__construct($file);

		$base = basename($file, '.zip');

		// Parse filename
		$underscore = strpos($base, '_');
		if ($underscore !== false)
		{
			$this->type = substr($base, 0, $underscore);
			$this->name = substr($base, $underscore + 1);
		}
		else
		{
			$this->name = $base;
		}

		// ================
		// Add file readers
		// ================
		$this->AddFileReader('namecheck.txt', FileNamecheck::class);
		$this->AddFileReader(['description.txt', 'credits.txt'], FileInfo::class);
		$this->AddFileReader(['version.txt', 'version.json'], FileVersion::class);
		$this->AddFileReader('rtbinfo.txt', FileRTBInfo::class);
		$this->AddFileTypeReader(['atmosphere', 'daycycle', 'ground', 'water'], FileConfig::class);
		$this->AddFileReader('gamemode.txt', FileConfig::class);
		$this->AddFileReader('colorset.txt', FileColorset::class);
		// These are to be added
		// png, jpg
		// dts
		// dsq
		// bls
		// blb
		// cs, gui
		// dml
		// ogg, wav

		// ==============
		// Add attributes
		// ==============
		$this->AddAttribute('type', function() { return $this->type; }, null);
		$this->AddAttribute('name', function() { return $this->name; }, null);
		$this->AddAttribute('title', function()
		{
			$title = $this->info->title;
			if ($title === null)
				$title = $this->rtbInfo->title;
			if ($title === null)
				$title = $this->name;
			return $title;
		}, function($value) { $this->info->title = $value; });
		$this->AddAttribute('authors', function() { return $this->info->authors; }, function($value) { $this->info->authors = $value; });
		$this->AddAttribute('authorsRaw', function() { return $this->info->authorsRaw; }, function($value) { $this->info->authorsRaw = $value; });

		$this->AddAttribute('info', function()
		{
			// Fallthrough to this single file
			if ($this->HasFile('credits.txt') && ($this->isSpeedkart || !$this->HasFile('description.txt')))
			{
				return $this->GetFile('credits.txt');
			}

			return $this->GetFile('description.txt', true);
		}, null);
		$this->AddAttribute('namecheck', function() { return $this->GetFile('namecheck.txt', true); }, null);
		$this->AddAttribute('version', function()
		{
			if ($this->HasFile('version.json'))
			{
				return $this->GetFile('version.json');
			}
			elseif ($this->HasFile('version.txt'))
			{
				return $this->GetFile('version.txt');
			}
			// Create it
			return $this->GetFile('version.json', true);
		}, null);
		$this->AddAttribute('rtbInfo', function() { return $this->GetFile('rtbInfo.txt', true); }, null);

		// ================
		// Attribute checks
		// ================
		$this->AddAttribute('isExecutable', function() { return !$this->isClient && !$this->isServer && $this->hasScripts; }, null);
		$this->AddAttribute('isClient', function() { return $this->HasFile('client.cs'); }, null);
		$this->AddAttribute('isServer', function() { return $this->HasFile('server.cs'); }, null);
		$this->AddAttribute('isGameMode', function() { return $this->HasFile('gamemode.txt'); }, null);
		$this->AddAttribute('isSpeedkart', function() { return strtolower($this->type) == 'speedkart'; }, null);
		$this->AddAttribute('hasInfo', function() { return $this->HasFile('description.txt') || $this->HasFile('credits.txt'); }, null);
		$this->AddAttribute('hasNamecheck', function() { return $this->HasFile('namecheck.txt'); }, null);
		$this->AddAttribute('hasColorset', function() { return $this->HasFile('colorset.txt'); }, null);
		$this->AddAttribute('hasScripts', function() { return $this->HasFileType('cs'); }, null);
		$this->AddAttribute('hasGUI', function() { return $this->HasFileType('gui'); }, null);
		$this->AddAttribute('hasBricks', function() { return $this->HasFileType('blb'); }, null); // Brick format
		$this->AddAttribute('hasMusic', function() { return $this->HasFileType('ogg'); }, null);
		$this->AddAttribute('hasSound', function() { return $this->HasFileType('wav'); }, null);
		$this->AddAttribute('hasImages', function() { return $this->HasFileType('png') || $this->HasFileType('jpg'); }, null);
		$this->AddAttribute('hasModels', function() { return $this->HasFileType('dts'); }, null); // Normal models
		$this->AddAttribute('hasAnimations', function() { return $this->HasFileType('dsq'); }, null);
		$this->AddAttribute('hasSave', function() { return $this->HasFileType('bls'); }, null);
		$this->AddAttribute('hasAtmosphere', function() { return $this->HasFileType('atmosphere'); }, null);
		$this->AddAttribute('hasSkyboxTexture', function() { return $this->HasFileType('dml'); }, null);
		$this->AddAttribute('hasDaycycle', function() { return $this->HasFileType('daycycle'); }, null);
		$this->AddAttribute('hasGround', function() { return $this->HasFileType('ground'); }, null);
		$this->AddAttribute('hasWater', function() { return $this->HasFileType('water'); }, null);
		// Deprecated file types
		$this->AddAttribute('hasDeprecatedFiles', function()
		{
			return $this->hasInteriors
				|| $this->hasTerrain
				|| $this->hasLight
				|| $this->hasMission;
		}, null);
		// Interior files. dts may be used instead
		$this->AddAttribute('hasInteriors', function() { return $this->HasFileType('dif'); }, null);
		// Terrain files. dts may be used instead
		$this->AddAttribute('hasTerrain', function() { return $this->HasFileType('ter'); }, null);
		// Light file
		$this->AddAttribute('hasLight', function() { return $this->HasFileType('ml'); }, null);
		// Mission file, use gamemode.txt, atmosphere and/or dml
		$this->AddAttribute('hasMission', function() { return $this->HasFileType('mis'); }, null);

		// RTB specific
		$this->AddAttribute('hasRTB', function() { return $this->HasFile('rtbInfo.txt'); }, null);

		// ===========
		// Type checks
		// ===========
		// These are the known types and what they contain. However,
		// only a few of them is guaranteed to be named this way
		// This system is only guessing
		// ===========
		$this->AddTypeCheck([
			'bot', 'brick', 'client', 'daycycle', 'decal', 'emote', 'event', 'face', 'gamemode',
			'ground', 'item', 'light', 'particle', 'player', 'print', 'projectile', 'script',
			'server', 'sky', 'sound', 'tool', 'vehicle', 'water', 'weapon'
		], function() { return $this->hasInfo; });
		$this->AddTypeCheck(['bot', 'script'], function() { return $this->isServer || $this->isClient; });
		$this->AddTypeCheck('brick', function() { return $this->isServer && $this->HasFileType('blb'); });
		$this->AddTypeCheck('client', function() { return $this->isClient; });
		$this->AddTypeCheck('daycycle', function() { return $this->hasDaycycle; });
		// TODO: Check for image sizes
		$this->AddTypeCheck(['decal', 'face'], function() { return $this->HasFolder('thumbs') && $this->HasFileType('png'); }); // Confirmed
		$this->AddTypeCheck(['emote', 'sound'], function() { return $this->isServer && $this->hasSound; });
		$this->AddTypeCheck('gamemode', function() { return $this->ValidateGameMode(); }); // Confirmed
		$this->AddTypeCheck('ground', function() { return $this->hasGround; });
		// Note: server.cs is only required as the add-on may use other mods resources
		$this->AddTypeCheck(['event', 'item', 'light', 'particle', 'player', 'projectile', 'server', 'tool', 'vehicle', 'weapon'], function() { return $this->isServer; });
		// Note: server.cs is required, but rarely used
		// TODO: Check for image sizes
		$this->AddTypeCheck('print', function() { return $this->isServer && $this->HasFolder('icons') && $this->HasFolder('prints') && $this->HasFileType('png'); }); // Confirmed
		$this->AddTypeCheck('sky', function() { return $this->hasAtmosphere; }); // Confirmed
		$this->AddTypeCheck('speedkart', function() { return $this->ValidateSpeedkart(); }); // Confirmed
		$this->AddTypeCheck('water', function() { return $this->hasWater; });
	}

	// Add a type to check against
	public function AddTypeCheck($types, $callback)
	{
		if (!empty($types) && is_callable($callback, true))
		{
			if (!is_array($types))
				$types = [$types];
			foreach ($types as $type)
			{
				$type = strtolower($type);
				if (!array_key_exists($type, $this->type_check))
					$this->type_check[$type] = [];
				$this->type_check[$type][] = $callback;
			}
		}
	}

	// Check type
	public function ValidateType($type = null)
	{
		if ($type === null)
			$type = $this->type;
		$type = strtolower($type);
		// No check, automatic pass
		if (!array_key_exists($type, $this->type_check))
			return true;

		foreach ($this->type_check[$type] as $func)
		{
			if (!call_user_func($func))
				return false;
		}

		return true;
	}

	// Check if type exists
	public function ValidateTypeExists($type = null)
	{
		if ($type === null)
			$type = $this->type;
		$type = strtolower($type);

		return array_key_exists($type, $this->type_check);
	}

	// Validates file to contain the required data
	public function Validate()
	{
		// Validation of types
		if ($this->ValidateTypeExists())
			return $this->ValidateType();

		// The next just checks what is in the archive and tries to guess whatever the type

		// Game Mode
		if ($this->isGameMode)
		{
			// Due to its existence, it is always a game mode, validate it
			return $this->ValidateGameMode();
		}

		// No script files
		if (!$this->hasScripts)
		{
			// Daycycle
			if ($this->hasDaycycle)
				return true;
			// Ground
			if ($this->hasGround)
				return true;
			// Water
			if ($this->hasWater)
				return true;
			// Sky
			if ($this->hasAtmosphere && $this->hasSkyboxTexture)
				return true;
			// Invalid add-on
			return false;
		}
		else
		{
			return $this->ValidateScripts();
		}
	}

	// Script validator
	public function ValidateScripts()
	{
		// Needs to be executed
		if ($this->isExecutable)
			return false;
		// TODO: Go through all scripts and verify that they are correct
		return true;
	}

	// Get out some extra files
	public function Cleanup()
	{
		parent::Cleanup();
		$this->RemoveFile('rtbcontent.txt'); // RTB cache file
		$this->RemoveFile('rtbinfo.txt'); // RTB file
	}

	// Fix info file, if possible
	public function FixInfo()
	{
		$info = $this->GetFile('description.txt', true);
		// Make sure it is valid
		if (!$info->Validate())
		{
			$info = $this->GetFile('credits.txt', true);
			if (!$info->Validate())
				return;
		}
		if ($this->isSpeedkart)
		{
			$this->RemoveFile('description.txt');
			$info->filename = 'credits.txt';
		}
		else
		{
			$this->RemoveFile('credits.txt');
			$info->filename = 'description.txt';
		}
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
		// Confirmed
		return $this->isGameMode
			&& $this->hasColorset
			&& $this->hasDescription
			&& $this->HasFile('save.bls')
			&& $this->HasFile('preview.jpg')
			&& $this->HasFile('thumb.jpg');
	}

	// Validate speedkart files
	public function ValidateSpeedkart()
	{
		// Confirmed
		return $this->isSpeedkart
			&& $this->HasFile('save.bls');
	}

	// Greek2me's Updater
	// Validate version.txt
	public function ValidateVersion()
	{
		// Only allow one file
		if ($this->HasFile('version.txt') && $this->HasFile('version.json'))
			return false;

		return $this->version->Validate();
	}

	// Fix version file, if possible
	public function FixVersion()
	{
		$version = $this->GetFile('version.json', true);
		// Make sure it is valid
		if (!$version->Validate())
		{
			$version = $this->GetFile('version.txt', true);
			if (!$version->Validate())
				return;
		}

		// Remove old files
		$this->RemoveFile('version.txt');
		$version->filename = 'version.json';
	}
}

?>
