<?php
namespace App\Repository\Archive;

class ArchiveFile
{
	protected $archive = '';
	protected $filename = '';
	private $content = '';

	public function __construct($archive_name, $filename)
	{
		$this->archive = $archive_name;
		$this->filename = $filename;
	}

	public function Filename()
	{
		return $this->filename;
	}

	public function ChangeFilename($new_name)
	{
		$this->filename = $new_name;
	}

	public function Read($content)
	{
		$this->content = $content;
	}

	public function Write()
	{
		return $this->content;
	}

	public function Validate()
	{
		return true;
	}
}

?>