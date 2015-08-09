<?php

class Input
{
	private $get = [];
	private $post = [];
	private $files = [];
	private $data = [];
	private $raw = null;
	private $raw_get = null;

	// Get the process method
	public function ProcessMethod()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

	// Loads all the data available as input
	public function Process()
	{
		switch ($this->ProcessMethod())
		{
		case 'POST':
			$this->RequestPost();
			// Fallthrough as you can still send get variables
		case 'GET':
			$this->RequestGet();
			break;
		}

		$this->CreateData();
	}

	// Move files to a special folder
	public function MoveFiles($folder)
	{
		if (substr($folder, -1) !== '/')
			$folder .= '/';
		$success = 0;
		foreach ($this->files as $name => $files)
		{
			foreach ($files as $i => $file)
			{
				$new_name = $folder.basename($file['name']);
				if ($file['error'] === 0 && 
					move_uploaded_file($file['tmp_name'], $new_name))
				{
					$this->files[$name][$i]['new_name'] = $new_name;
					++$success;
				}
			}
		}

		// Recreate the data
		if ($success > 0)
			$this->CreateData();

		return $success;
	}

	// Get the amount of files uploaded
	public function FileCount($name_or_quick = null)
	{
		// Quick version
		if ($name_or_quick === true)
			return count($this->files);
		// Only go through the one with name
		if ($name_or_quick !== null)
			return isset($this->files[$name_or_quick]) ? count($this->files[$name_or_quick]) : 0;
		// Go through all files
		$amount = 0;
		foreach ($this->files as $files)
			$amount += count($files);
		return $amount;
	}

	// How many file errors that occured
	public function FileErrors()
	{
		$amount = 0;
		foreach ($this->files as $files)
			foreach ($files as $file)
				if ($file['error'] !== 0)
					++$amount;
		return $amount;
	}

	public function Get()
	{
		return $this->get;
	}

	public function Post()
	{
		return $this->post;
	}

	public function Files()
	{
		return $this->files;
	}

	public function Data()
	{
		return $this->data;
	}

	public function Raw()
	{
		return $this->raw;
	}

	public function RawGet()
	{
		return $this->raw_get;
	}

	// Request the get variables
	private function RequestGet()
	{
		$this->raw_get = $_SERVER['QUERY_STRING'];
		if (empty($this->raw_get))
			return;

		parse_str($this->raw_get, $this->get);
	}

	// Request the post variables
	private function RequestPost()
	{
		// May contain a boundary, so skipping that part
		list($type) = explode(';', $_SERVER['CONTENT_TYPE'], 2);

		switch (strtolower($type))
		{
		// Normal way to do it
		case 'application/x-www-form-urlencoded':
			$this->raw = file_get_contents('php://input');
			parse_str($this->raw, $this->post);
			break;
		// Sent files
		case 'multipart/form-data':
			$this->post = $_POST;

			foreach ($_FILES as $name => $data)
			{
				$this->files[$name] = [];
				// Multiple files
				if (is_array($data['name']))
				{
					// Convert to a more convenient format
					foreach ($data as $key => $dat)
						foreach ($dat as $i => $val)
							$this->files[$name][$i][$key] = $val;
				}
				// Single file
				else
				{
					array_push($this->files[$name], $data);
				}
			}
			break;
		// Special JSON case
		case 'application/json':
			$this->raw = file_get_contents('php://input');
			$this->post = json_decode($this->raw);
			break;
		// Handle unknown data
		default:
			// Just store it directly
			$this->raw = file_get_contents('php://input');
			break;
		}
	}

	// Create data from collected information
	private function CreateData()
	{
		// Merge get, post and files
		// Note: They will overwrite each other. Adding to a list could be an option.
		$this->data = array_merge($this->get, $this->post, $this->files);
	}
}

?>