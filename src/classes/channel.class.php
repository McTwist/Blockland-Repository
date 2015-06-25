<?php
/*
 * Channel
 * Keeps the information about the channel
 * This also includes all information about the add-on in it
 */

class Channel
{
	private $channelId = null;
	private $channelName = '*';

	private $version = '0.0';
	private $restartRequired = null;
	private $file = null;
	private $changelog = null;

	const TABS = "\t\t";
	const TABS2 = "\t\t\t";

	public function __construct($id = null, $name = '*', $file = null)
	{
		if (empty($name))
			$name = '*';
		$this->channelId = null;
		$this->channelName = $name;
		$this->file = $file;
	}

	public function Id()
	{
		return $this->channelId;
	}

	public function Name()
	{
		return $this->channelName;
	}

	// Print in Torque Markup Language
	public function PrintTML($pretty = false)
	{
		// No file, avoid showing it
		if ($this->file === null)
			return '';

		$data = '';
		if ($pretty) $data .= self::TABS;
		$data .= "<channel:{$this->channelName}>";
		if ($pretty) $data .= "\n";

		// Version
		if ($pretty) $data .= self::TABS2;
		$data .= "<version:{$this->version}>";
		if ($pretty) $data .= "\n";

		// Restart required
		if ($this->restartRequired !== null)
		{
			if ($pretty) $data .= self::TABS2;
			$data .= "<restartRequired:{$this->restartRequired}>";
			if ($pretty) $data .= "\n";
		}

		// File
		if ($pretty) $data .= self::TABS2;
		$data .= "<file:{$this->file}>";
		if ($pretty) $data .= "\n";

		// Change log
		if ($this->changelog !== null)
		{
			if ($pretty) $data .= self::TABS2;
			$data .= "<changelog:{$this->changelog}>";
			if ($pretty) $data .= "\n";
		}

		// Description
		/*if ($this->desc !== null)
		{
			if ($pretty) $data .= self::TABS2;
			$data .= "<desc:{$this->desc}>";
			if ($pretty) $data .= "\n";
		}*/

		if ($pretty) $data .= self::TABS;
		$data .= "</channel>";
		if ($pretty) $data .= "\n";
		return $data;
	}

	// Print in JSON
	public function PrintJSON($pretty = false, $as_struct = false)
	{
		$channel = new stdClass;
		// No file, avoid showing it
		if ($this->file === null)
			return self::MakeJSON($channel, $as_struct);

		$channel->channel = $this->channelName;

		// Version
		$channel->version = $this->version;

		// Restart required
		if ($this->restartRequired !== null)
			$channel->restartRequired = $this->restartRequired;

		// File
		$channel->file = $this->file;

		// Change log
		if ($this->changelog !== null)
			$channel->changelog = $this->changelog;

		// Description
		/*if ($this->desc !== null)
			$channel->desc = $this->desc;*/

		return self::MakeJSON($channel, $pretty, $as_struct);
	}

	static private function MakeJSON($data, $pretty, $as_struct)
	{
		return $as_struct ? (object)$data : json_encode((object)$data, ($pretty ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES);
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
}

?>