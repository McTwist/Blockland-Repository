<?php

class Session
{
	private $id = null;
	private $configs = [];
	private $variables = [];

	// Check if started
	public function IsStarted()
	{
		return session_id() !== '';
	}

	// Start a session
	public function Start($name = null, $id = null, array $config = null)
	{
		// Set the name of the key
		if ($name !== null)
			session_name($name);

		// Set the id for the value
		if ($id !== null)
			session_id($id);

		if ($config !== null)
		{
			$this->configs = $config;
			$params = session_get_cookie_params();
			// Merge them and put the new values over it
			$configs = array_merge($params, $config);
			session_set_cookie_params(
				$configs['lifetime'],
				$configs['path'],
				$configs['domain'],
				$configs['secure'],
				$configs['httponly']);
		}

		session_start();

		$this->id = session_id();

		$this->Load();
	}

	// Stop the session
	public function Stop()
	{
		session_write_close();
	}

	// Destroy the current session along with its variables
	public function Destroy()
	{
		// Create a new ID
		session_regenerate_id(true);
		// Clear the variables
		$this->Clear();
		// Destroy it
		session_destroy();
	}

	// Reset the session variables
	public function Clear()
	{
		session_unset();
		$this->variables = [];
	}

	// Revert changes and stop session
	public function Abort()
	{
		if (function_exists('session_abort'))
			session_abort(); // 5.6
		else
		{
			$this->Reset();
			$this->Stop();
		}
	}

	// Reset variables to previous
	public function Reset()
	{
		if (function_exists('session_reset'))
			session_reset(); // 5.6
		else
			$_SESSION = $this->variables;
	}

	// Load a session
	private function Load()
	{
		// Store them away for later use
		$this->variables = $_SESSION;
		$this->Touch();
	}

	// Touch the session, updating the last access time
	private function Touch()
	{
		// This ensures that the session wont time out
		$_SESSION['__last_access__'] = time();
	}

	public function Id()
	{
		return $this->id;
	}

	public function Get($name)
	{
		return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
	}

	public function Set($name, $val)
	{
		$_SESSION[$name] = $val;
	}
}

?>