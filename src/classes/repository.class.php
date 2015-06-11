<?php
/*
 * Repository
 * The repository that contains all the add-ons
 */

require 'addon.class.php';

class Repository
{
	private $repositoryName = null;
	private $addons = [];

	public function __construct($name = null)
	{
		$this->repositoryName = $name;
	}

	// Add add-on to the repository
	public function AddAddOn(AddOn $addon)
	{
		array_push($this->addons, $addon);
	}

	public function GetName()
	{
		return $this->repositoryName;
	}

	public function PrintFormat()
	{
		$data = '<repository';
		if ($this->repositoryName !== null)
			$data .= ":{$this->repositoryName}";
		$data .= ">\n";
		foreach ($this->addons as $addon)
		{
			$data .= $addon->PrintFormat();
		}
		$data .= '</repository>';
		return $data;
	}

	// Object iteration
	public function rewind()
	{
		reset($this->addons);
	}
	public function current()
	{
		return current($this->addons);
	}
	public function key()
	{
		return key($this->addons);
	}
	public function next()
	{
		return next($this->addons);
	}
	public function valid()
	{
		$key = key($this->addons);
		return $key !== null && $key !== false;
	}
}

?>