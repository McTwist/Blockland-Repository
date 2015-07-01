<?php
/*
 * AddOn
 * Keeps a list of channels in it which contains this add-ons data
 */

require 'channel.class.php';

class AddOn
{
	private $addonId = null;
	private $addonName = null;
	private $description = null;

	private $channels = [];

	const TABS = "\t";
	const TABS2 = "\t\t";

	public function __construct($id = null, $name = null, $description = null)
	{
		$this->addonId = $id;
		$this->addonName = $name;
		$this->description = $description;
	}

	public function AddChannel(Channel $channel)
	{
		array_push($this->channels, $channel);
	}

	public function Id()
	{
		return $this->addonId;
	}

	public function Name()
	{
		return $this->addonName;
	}

	public function GetDescription()
	{
		return $this->description;
	}

	// Print in Torque Markup Language
	public function PrintTML($pretty = false)
	{
		if (empty($this->addonName))
			return '';

		$channels = '';
		foreach ($this->channels as $channel)
		{
			$channels .= $channel->PrintTML($pretty);
		}
		if (empty($channels))
			return '';

		$data = '';
		if ($pretty) $data .= self::TABS;
		$data .= "<addon:{$this->addonName}>";
		if ($pretty) $data .= "\n";

		// Description
		if ($this->description !== null)
		{
			if ($pretty) $data .= self::TABS2;
			$data .= "<desc:{$this->description}>";
			if ($pretty) $data .= "\n";
		}

		$data .= $channels;
		if ($pretty) $data .= self::TABS;
		$data .= "</addon>";
		if ($pretty) $data .= "\n";
		return $data;
	}

	// Print in JSON
	public function PrintJSON($pretty = false, $as_struct = false)
	{
		$addon = new stdClass;
		if (empty($this->addonName))
			return self::MakeJSON($addon, $as_struct);

		$addon->name = $this->addonName;
		if ($this->description !== null)
			$addon->description = $this->description;
		$addon->channels = [];
		foreach ($this->channels as $channel)
		{
			$channel = $channel->PrintJSON($pretty, $as_struct);
			if (isset($channel->file))
				array_push($addon->channels, $channel);
		}

		return self::MakeJSON($addon, $pretty, $as_struct);
	}

	static private function MakeJSON($data, $pretty, $as_struct)
	{
		return $as_struct ? (object)$data : json_encode((object)$data, ($pretty ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES);
	}

	// Object iteration
	public function rewind()
	{
		reset($this->channels);
	}
	public function current()
	{
		return current($this->channels);
	}
	public function key()
	{
		return key($this->channels);
	}
	public function next()
	{
		return next($this->channels);
	}
	public function valid()
	{
		$key = key($this->channels);
		return $key !== null && $key !== false;
	}
}

?>