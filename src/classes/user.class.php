<?php

class User
{
	private $id = 0;
	private $username = '';

	public function __construct($id, $username)
	{
		$this->id = $id;
		$this->username = $username;
	}

	public function Name()
	{
		return $this->username;
	}
}

?>