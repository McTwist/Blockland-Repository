<?php

class Cookie
{
	private $name = null;
	private $value = null;
	private $expire = 0;
	private $path = null;
	private $domain = null;
	private $secure = false;
	private $httponly = false;

	public function __construct($name)
	{
		// Get and store value
		if (isset($_COOKIE[$name]))
			$this->value = $_COOKIE[$name];
		$this->name = $name;

		// Set path
		$this->path = dirname($_SERVER['SCRIPT_NAME']);

		// Set domain
		$this->domain = $_SERVER['SERVER_NAME'];
	}

	// Remove cookie from client
	public function Remove()
	{
		// We're time traveling
		$this->expire = time() - 3600;
		// So no one can use it
		$this->value = null;
		// And apparently a path needs to be set
		$this->path = '/';
		// Remove the cookie so you can't get it again
		unset($_COOKIE[$this->name]);
		$this->Update(true);
	}

	// The value stored in the cookie
	public function Value($value = null)
	{
		return $this->Modify('value', $value);
	}

	// When the cookie will expire
	public function Expire($value = null)
	{
		return $this->Modify('expire', $value);
	}

	// The path in the uri
	public function Path($value = null)
	{
		return $this->Modify('path', $value);
	}

	// The domain it's conencted to
	public function Domain($value = null)
	{
		return $this->Modify('domain', $value);
	}

	// Using secure connection
	public function Secure($value = null)
	{
		return $this->Modify('secure', $value);
	}

	// Only through http
	public function HttpOnly($value = null)
	{
		return $this->Modify('httponly', $value);
	}

	// Smart way to hide functionality that is same everywhere anyway
	private function Modify($name, $value = null)
	{
		$var = $this->$name;
		if ($value !== null)
		{
			$this->$name = $value;
			$this->Update();
		}
		return $var;
	}

	// Update cookie with current values
	private function Update($force = false)
	{
		if ($this->value !== null || $force)
			setcookie(
				$this->name, 
				$this->value, 
				$this->expire, 
				$this->path, 
				$this->domain, 
				$this->secure, 
				$this->httponly);
	}
}

?>