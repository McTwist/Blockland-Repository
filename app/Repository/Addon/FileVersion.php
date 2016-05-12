<?php

namespace App\Repository\Addon;

/*
 * FileVersion
 * Handles the version file
 * Greek2me's Updater
 */
class FileVersion
{
	private $version = '0.0';
	private $channel = '*';
	private $repositories = [];

	const NL = File::NL;

	// Read version
	public function Read($content, $json)
	{
		$this->repositories = [];

		// Old version.txt format
		if (!$json)
		{
			// Split up the lines into an array
			$lines = preg_split('/$\R?^/m', $content);

			// Split up the fields internally into arrays
			array_walk($lines, function(&$value, $i) { $value = preg_split('/\s+/', trim($value)); });

			// Default values
			$format = null;
			$id = null;
			foreach ($lines as $line)
			{
				// Avoid problem
				if (count($line) < 2)
					continue;
				// Note: Values are taken directly from the Updater
				switch ($line[0])
				{
				case 'version': case 'version:': case 'vers':
					$this->version = $line[1];
					break;
				case 'channel': case 'channel:': case 'chan':
					$this->channel = $line[1];
					break;
				case 'repository': case 'repository:': case 'repo':
					$this->AddRepository(
						$line[1],
						count($line) > 2 ? $line[2] : null,
						count($line) > 3 ? $line[3] : null);
					break;
				case 'format': case 'format:': case 'form':
					$format = $line[1];
					break;
				case 'id': case 'id:':
					$id = (int)$line[1];
					break;
				}
			}

			// Set defaults
			foreach ($this->repositories as &$repo)
			{
				if (!isset($repo->format))
					$repo->format = $format;
				if (!isset($repo->id))
					$repo->id = $id;
			}
		}
		// New JSON format
		else
		{
			$data = json_decode($content);

			// Could not decode
			if ($data === null)
				return;

			$this->repositories = [];

			if (isset($data->version))
				$this->version = $data->version;
			if (isset($data->channel))
				$this->channel = $data->channel;

			if (isset($data->repositories))
			{
				foreach ($data->repositories as $repository)
				{
					if (isset($repository->url))
					{
						$this->AddRepository(
							$repository->url,
							isset($repository->format) ? $repository->format : null,
							isset($repository->id) ? $repository->id : null);
					}
				}
			}
		}
	}

	// Validate version
	public function Validate()
	{
		return !empty($this->version)
			&& !empty($this->channel)
			&& count($this->repositories) > 0;
	}

	// Generate a version file
	// Pretty only works with JSON
	public function Generate($json = true, $pretty = true)
	{
		// Check a couple of restrainments
		if (empty($this->version) || empty($this->channel) || count($this->repositories) == 0)
			return '';

		// Old version
		if (!$json)
		{
			// Prepare data
			$content  = "version {$this->version}".self::NL;
			$content .= "channel {$this->channel}".self::NL;
			foreach ($this->repositories as $url => $repo)
			{
				$content .= "repository {$url} {$url}";
				if (isset($repo->format))
					$content .= " {$repo->format}";
				if (isset($repo->id))
					$content .= " {$repo->id}";
				$content .= self::NL;
			}
		}
		// New format
		else
		{
			// Prepare data
			$data = new \stdClass();
			$data->version = $this->version;
			$data->channel = $this->channel;
			$data->repositories = [];
			foreach ($this->repositories as $url => $repo)
			{
				// Note: New object is required to sort the data
				$rep = new \stdClass();
				$rep->url = $url;
				if (isset($repo->format))
					$rep->format = $repo->format;
				if (isset($repo->id))
					$rep->id = $repo->id;
				array_push($data->repositories, $rep);
			}

			// Generate content
			$content = json_encode($data, ($pretty ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES);
		}

		return $content;
	}

	public function Version($value = null)
	{
		$version = $this->version;
		if ($value !== null)
			$this->version = $value;
		return $version;
	}

	public function Channel($value = null)
	{
		$channel = $this->channel;
		if ($value !== null)
			$this->channel = $value;
		return $channel;
	}

	public function AddRepository($url, $format = null, $id = null)
	{
		if (isset($this->repositories[$url]))
			return $this->SetRepository($url, $format, $id);

		$repo = new \stdClass();
		$repo->format = $format;
		$repo->id = $id;
		$this->repositories[$url] = $repo;
	}

	public function SetRepository($url, $format = null, $id = null)
	{
		if (!isset($this->repositories[$url]))
			return $this->AddRepository($url, $format, $id);

		if (isset($format))
			$this->repositories[$url]->format = $format;
		if (isset($id))
			$this->repositories[$url]->id = $id;
	}

	public function Repositories()
	{
		return $this->repositories;
	}
}

?>