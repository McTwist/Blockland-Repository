<?php
/*
 * AddOn
 * Keeps a list of channels in it which contains this add-ons data
 */

require 'channel.class.php';

class AddOn
{
	private $addonName = '';
	private $channels = [];

	const TABS = "\t";

	public function __construct($name)
	{
		$this->addonName = $name;
	}

	public function AddChannel(Channel $channel)
	{
		array_push($this->channels, $channel);
	}

	public function GetName()
	{
		return $this->addonName;
	}

	public function PrintFormat()
	{
		if (empty($this->addonName))
			return '';

		$channels = '';
		foreach ($this->channels as $channel)
		{
			$channels .= $channel->PrintFormat();
		}
		if (empty($channels))
			return '';

		$data = self::TABS."<addon:{$this->addonName}>\n";
		$data .= $channels;
		$data .= self::TABS."</addon>\n";
		return $data;
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