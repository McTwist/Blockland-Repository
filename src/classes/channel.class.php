<?php
/*
 * Channel
 * Keeps the information about the channel
 * This also includes all information about the add-on in it
 */

class Channel
{
	private $channelName = '*';

	private $version = '0.0';
	private $restartRequired = null;
	private $file = null;
	private $changelog = null;
	private $desc = null;

	const TABS = "\t\t";
	const TABS2 = "\t\t\t";

	public function __construct($name = '*')
	{
		$this->channelName = $name;
	}

	public function GetName()
	{
		return $this->channelName;
	}

	public function PrintFormat()
	{
		// No file, avoid showing it
		if ($this->file === null)
			return '';

		$data = self::TABS."<channel:{$this->channelName}>\n";

		// Version
		$data .= self::TABS2."<version:{$this->version}>\n";

		// Restart required
		if ($this->restartRequired !== null)
			$data .= self::TABS2."<restartRequired:{$this->restartRequired}>\n";

		// File
		$data .= self::TABS2."<file:{$this->file}>\n";

		// Change log
		if ($this->changelog !== null)
			$data .= self::TABS2."<changelog:{$this->changelog}>\n";

		// Description
		if ($this->desc !== null)
			$data .= self::TABS2."<desc:{$this->desc}>\n";

		$data .= self::TABS."<channel>\n";
		return $data;
	}

	// Set values to be used
	public function SetVersion($version)
	{
		$this->version = $version;
	}
	public function SetRestartRequired($restartRequired)
	{
		$this->restartRequired = $restartRequired;
	}
	public function SetFile($file)
	{
		$this->file = $file;
	}
	public function SetChangelog($changelog)
	{
		$this->changelog = $changelog;
	}
	public function SetDesc($desc)
	{
		$this->desc = $desc;
	}

	// Get values from channel
	public function GetVersion()
	{
		return $this->version;
	}
	public function GetRestartRequired()
	{
		return $this->restartRequired;
	}
	public function GetFile()
	{
		return $this->file;
	}
	public function GetChangelog()
	{
		return $this->changelog;
	}
	public function GetDesc()
	{
		return $this->desc;
	}
}

?>