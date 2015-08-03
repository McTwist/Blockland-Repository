<?php
/*
 * Repository
 * The repository that contains all the add-ons
 */

require 'addon.class.php';

class Repository
{
	private $repositoryId = null;
	private $repositoryName = null;
	private $addons = [];

	public function __construct($id = null, $name = null)
	{
		$this->repositoryId = $id;
		$this->repositoryName = $name;
	}

	// Add add-on to the repository
	public function AddAddOn(AddOn $addon)
	{
		array_push($this->addons, $addon);
	}

	public function GetAddOns()
	{
		return $this->addons;
	}

	public function Id()
	{
		return $this->repositoryId;
	}

	public function Name()
	{
		return $this->repositoryName;
	}

	// Print in Torque Markup Language
	public function PrintTML($pretty = false)
	{
		$data = '<repository';
		if ($this->repositoryName !== null)
			$data .= ":{$this->repositoryName}";
		$data .= '>';
		if ($pretty) $data .= "\n";
		foreach ($this->addons as $addon)
		{
			$data .= $addon->PrintTML($pretty);
		}
		$data .= '</repository>';
		return $data;
	}

	// Print in JSON
	public function PrintJSON($pretty = false, $as_struct = false)
	{
		$data = [];
		if ($this->repositoryName !== null)
			$data['name'] = $this->repositoryName;

		$data['add-ons'] = [];
		foreach ($this->addons as $addon)
		{
			$addon = $addon->PrintJSON($pretty, true);
			if (isset($addon->name))
				array_push($data['add-ons'], $addon);
		}

		return self::MakeJSON($data, $pretty, $as_struct);
	}

	static private function MakeJSON($data, $pretty, $as_struct)
	{
		return $as_struct ? (object)$data : json_encode((object)$data, ($pretty ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES);
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