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
	// name - The name of the session (variable name)
	// id - The id of the session (variable value)
	// array - Special extra configs
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
				isset($configs['httponly']));
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
		// Clear the variables
		$this->Clear();
		// Clear the cookie
		if (isset($_COOKIE[session_name()]))
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', 
				1, 
				$params['path'], 
				$params['domain'], 
				$params['secure'], 
				isset($params['httponly']));
		}
		// Create a new ID
		session_regenerate_id(true);
		// Destroy it
		session_destroy();
	}

	// Clear the session variables
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

	// Reset variables to previous state
	public function Reset()
	{
		if (function_exists('session_reset'))
			session_reset(); // 5.6
		else
			$_SESSION = $this->variables;
	}

	// Secure the data by regenerating
	// Put in true iff there is only one script currently called
	public function Regenerate($delete = false)
	{
		session_regenerate_id($delete);
		$new_session = session_id();
		session_write_close();
		session_id($new_session);
		session_start();
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

	public function Remove($name)
	{
		unset($_SESSION[$name]);
	}

	public function Exist($name)
	{
		return isset($_SESSION[$name]);
	}
}

?>