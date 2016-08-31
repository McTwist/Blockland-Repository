<?php
namespace App\Repository\Archive;

class ArchiveFile
{
	protected $archive = '';
	protected $filename = '';
	private $old_filename = '';
	private $content = '';

	public function __construct($archive_name, $filename)
	{
		$this->archive = $archive_name;
		$this->old_filename = $filename;
		$this->filename = $filename;
	}

	public function Filename()
	{
		return $this->filename;
	}

	public function PreviousFilename()
	{
		return $this->old_filename;
	}

	public function ChangeFilename($new_name)
	{
		$this->filename = $new_name;
	}

	public function Set($content)
	{
		$this->content = $content;
	}

	public function Get()
	{
		return $this->content;
	}

	public function Validate()
	{
		return true;
	}
}

?>