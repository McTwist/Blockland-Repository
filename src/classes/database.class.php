<?php
/*
 * Database
 * Creates objects retrieved from the database
 */

require 'user.class.php';

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

	// Get user from id
	public function GetUser($id)
	{
		$stmt = $this->db->prepare('SELECT (name) FROM users WHERE id=?');
		if (!$stmt->execute(array($id)))
			return null;

		$obj = $stmt->fetchObject();
		return new User($id, $obj->name);
	}
}

?>