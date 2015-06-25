<?php
/*
 * Database
 * Creates objects retrieved from the database
 */

require 'user.class.php';
require 'repository.class.php';
require 'password.class.php';

class Database
{
	private $db = null;

	public function __construct($file)
	{
		// Get file content
		$json = file_get_contents($file);
		if ($json === false)
			throw new Exception("Unable to open file: {$file}");

		// Decode preferences
		$pref = json_decode($json);
		if ($pref === null)
			throw new Exception("File is not valid: {$file}, ".'"'.json_last_error().'"');

		// Prepare port if it exists
		$port = (isset($pref->database->port)) ? ";port={$pref->database->port}" : '';

		// Start the connection
		$this->db = new PDO(
			"{$pref->database->driver}:host={$pref->database->host}{$port};dbname={$pref->database->db}",
			$pref->database->username,
			$pref->database->password);
	}

	// Get users
	public function GetUsers()
	{
		$stmt = $this->db->prepare('SELECT id, name FROM users');
		if (!$stmt->execute())
			return [];

		$users = [];
		while ($obj = $stmt->fetchObject())
			array_push($users, new User($obj->id, $obj->name));

		return $users;
	}

	// Get user from id
	public function GetUser($name_id)
	{
		if (is_string($name_id))
		{
			$stmt = $this->db->prepare('SELECT id, name FROM users WHERE searchable_name=?');
			$data = array(mb_strtolower($name_id));
		}
		else
		{
			$stmt = $this->db->prepare('SELECT id, name FROM users WHERE id=?');
			$data = array($name_id);
		}
		if (!$stmt->execute($data))
			return null;

		if ($obj = $stmt->fetchObject())
			return new User($obj->id, $obj->name);
		else
			return null;
	}

	// Get list of addons and its channels from a string
	public function GetAddOnString($str)
	{
		// Filter out the bad bits and get a valid list
		$list = array_map('intval', array_filter(explode('-', $str), 'is_numeric'));
		return $this->GetAddOnList($list);
	}

	// Get list of addons and its channels
	public function GetAddOnList($list)
	{
		if (is_string($list))
			$list = explode('-', $list);
		elseif (is_object($list))
			$list = array_values((array)$list);
		elseif (!is_array($list))
			$list = array($list);

		// Prepare IN statement
		$in_query = implode(', ', array_fill(0, count($list), '?'));

		// Get ready for the huge selection statement
		$query = 'SELECT
			repo.id AS repo_id,
			repo.name AS repo_name,
			addons.id AS addon_id,
			addons.name AS addon_name,
			addons.description AS description,
			channels.id AS channel_id,
			channels.name AS channel_name,
			channels.version AS channel_version,
			channels.restart_required AS channel_restart_required,
			channels.changelog AS channel_changelog,
			data.file AS file
			FROM addons
			LEFT JOIN repositories AS repo ON repo.id=addons.repository_id
			LEFT JOIN addon_channels AS channels ON addons.id=channels.addon_id
			LEFT JOIN addon_data AS data ON data.id=channels.data
			WHERE addons.id IN ('.$in_query.')';

		$stmt = $this->db->prepare($query);
		if (!$stmt->execute($list))
		{
			print_r($stmt->errorInfo());
			return null;
		}
		$result = $stmt->fetchAll(PDO::FETCH_CLASS, "stdClass");

		$repos = [];
		$addons = [];
		$channels = [];
		foreach ($result as $obj)
		{
			// Found repository
			if (!isset($repos[$obj->repo_id]))
			{
				$repos[$obj->repo_id] = new Repository($obj->repo_id, $obj->repo_name);
			}
			// Found add-on
			if (!isset($addons[$obj->addon_id]))
			{
				$desc = $obj->description;
				if (empty($desc))
					$desc = null;
				$addons[$obj->addon_id] = new AddOn($obj->addon_id, $obj->addon_name, $desc);
				$repos[$obj->repo_id]->AddAddOn($addons[$obj->addon_id]);
			}
			// Found channel
			if (!isset($channels[$obj->channel_id]))
			{
				$channels[$obj->channel_id] = new Channel($obj->channel_id, $obj->channel_name, $obj->file);
				if (isset($obj->channel_version))
					$channels[$obj->channel_id]->SetVersion($obj->channel_version);
				if (isset($obj->channel_restart_required))
					$channels[$obj->channel_id]->SetRestartRequired($obj->channel_restart_required);
				if (isset($obj->channel_changelog))
					$channels[$obj->channel_id]->SetChangelog($obj->channel_changelog);
				$addons[$obj->addon_id]->AddChannel($channels[$obj->channel_id]);
			}
		}

		return count($repos) ? array_shift($repos) : null;
	}

	// Get all repositories available
	public function GetRepositories()
	{
		$stmt = $this->db->prepare('SELECT id, name FROM repositories');
		if (!$stmt->execute())
			return [];

		$repos = [];
		while ($obj = $stmt->fetchObject())
			array_push($repos, new Repository($obj->id, $obj->name));

		foreach ($repos as &$repo)
			foreach ($this->GetAddOns($repo->Id()) as $addon)
				$repo->AddAddOn($addon);

		return $repos;
	}

	// Get repository
	public function GetRepository($name = null)
	{
		$stmt = $this->db->prepare('SELECT id, name FROM repositories WHERE name=?');
		if (!$stmt->execute(array($name)))
			return null;

		if ($obj = $stmt->fetchObject())
			$repo = new Repository($obj->id, $obj->name);
		else
			return null;

		foreach ($this->GetAddOns($obj->id) as $addon)
			$repo->AddAddOn($addon);

		return $repo;
	}

	// Get add-ons in repository
	public function GetAddOns($repository_id)
	{
		$stmt = $this->db->prepare('SELECT id, name FROM addons WHERE repository_id=?');
		if (!$stmt->execute(array($repository_id)))
			return [];

		$addons = [];
		while ($obj = $stmt->fetchObject())
			array_push($addons, new AddOn($obj->id, $obj->name));

		foreach ($addons as $i => $addon)
			foreach ($this->GetChannels($addon->Id()) as $channel)
				$addons[$i]->AddChannel($channel);

		return $addons;
	}

	// Get add-on depending on name or id
	public function GetAddOn($name_id)
	{
		if (is_string($name_id))
		{
			$stmt = $this->db->prepare('SELECT id, name FROM addons WHERE searchable_name=?');
			$data = array(mb_strtolower($name_id));
		}
		else
		{
			$stmt = $this->db->prepare('SELECT id, name FROM addons WHERE id=?');
			$data = array($name_id);
		}
		if (!$stmt->execute($data))
			return null;

		if ($obj = $stmt->fetchObject())
			$addon = new AddOn($obj->id, $obj->name);
		else
			return null;

		foreach ($this->GetChannels($obj->id) as $channel)
			$addon->AddChannel($channel);

		return $addon;
	}

	// Get channels in add-on
	public function GetChannels($addon_id)
	{
		$stmt = $this->db->prepare('SELECT id, name, (SELECT file FROM addon_data WHERE id=addon_channels.data) AS file FROM addon_channels WHERE addon_id=?');
		if (!$stmt->execute(array($addon_id)))
			return [];

		$channels = [];
		while ($obj = $stmt->fetchObject())
			array_push($channels, new Channel($obj->id, $obj->name, $obj->file));

		return $channels;
	}

	// Get add-on channel depending on name or id
	public function GetChannel($name_id)
	{
		if (is_string($name_id))
		{
			$stmt = $this->db->prepare('SELECT id, name, (SELECT file FROM addon_data WHERE id=addon_channels.data) AS file FROM addon_channels WHERE name=?');
			$data = array(mb_strtolower($name_id));
		}
		else
		{
			$stmt = $this->db->prepare('SELECT id, name, (SELECT file FROM addon_data WHERE id=addon_channels.data) AS file FROM addon_channels WHERE id=?');
			$data = array($name_id);
		}
		if (!$stmt->execute($data))
			return null;

		if ($obj = $stmt->fetchObject())
			$addon = new Channel($obj->id, $obj->name, $obj->file);
		else
			return null;

		return $addon;
	}

	// Create a user
	public function CreateUser($name, $password, $email)
	{
		if (!$this->ValidateName($name))
			return false;

		$pass = new Password();
		$hash = $pass->Create($password);

		$stmt = $this->db->prepare('INSERT INTO users (searchable_name, name, password, email) VALUES (?, ?, ?, ?)');
		if (!$stmt->execute(array(mb_strtolower($name), $name, $hash, $email)))
			return false;
		return $stmt->rowCount() > 0;
	}

	// Create a repository
	public function CreateRepository($name)
	{
		if (empty($name))
			return false;

		$stmt = $this->db->prepare('INSERT INTO repositories (name) VALUES (?)');
		if (!$stmt->execute(array($name)))
			return false;
		return $stmt->rowCount() > 0;
	}

	// Create an add-on
	public function CreateAddOn($repo, $name, $description = null)
	{
		if (empty($name))
			return false;

		$stmt = $this->db->prepare('INSERT INTO addons (searchable_name, name, repository_id, description) VALUES (?, ?, ?, ?)');
		if (!$stmt->execute(array(mb_strtolower($name), $name, $repo, $description)))
			return false;
		return $stmt->rowCount() > 0;
	}

	// Create a channel
	public function CreateChannel($addon, $name, $version, $file, $restart = null, $changelog = null)
	{
		if (empty($name))
			$name = '*';
		if (empty($restart))
			$restart = null;
		if (empty($changelog))
			$changelog = null;

		$this->db->beginTransaction();

		$stmt = $this->db->prepare('INSERT INTO addon_data (file) VALUES (?)');
		if (!$stmt->execute(array($file)))
		{
			$this->db->rollBack();
			return false;
		}

		$last_id = $this->db->lastInsertId();

		$stmt = $this->db->prepare(
			'INSERT INTO addon_channels 
			(name, addon_id, version, restart_required, data, changelog) 
			VALUES (?, ?, ?, ?, ?, ?)');
		if (!$stmt->execute(array($name, $addon, $version, $restart, $last_id, $changelog)))
		{
			$this->db->rollBack();
			return false;
		}

		return $this->db->commit();
	}

	// Add owner to add-on
	public function AddAddOnOwner($addon, $user)
	{
		$stmt = $this->db->prepare('INSERT INTO addon_owner (addon_id, user_id) VALUES (?, ?)');
		if (!$stmt->execute(array($addon, $user)))
			return false;
		return $stmt->rowCount() > 0;
	}

	// Verify user name
	private function ValidateName($name)
	{
		return !empty($name);
	}
}

?>