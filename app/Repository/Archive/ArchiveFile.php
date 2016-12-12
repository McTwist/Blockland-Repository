<?php
namespace App\Repository\Archive;

class ArchiveFile
{
	use ArchiveAttributes;

	protected $archive = null;
	protected $filename = '';
	private $old_filename = '';
	private $content = '';

	public function __construct($archive, $filename)
	{
		$this->archive = $archive;
		$this->old_filename = $filename;
		$this->filename = $filename;

		$this->AddAttribute('filename', function() { return $this->filename; }, function($value) { $this->filename = $value; });
		$this->AddAttribute('previousFilename', function() { return $this->old_filename; }, null);
		$this->AddAttribute('changedFilename', function() { return $this->filename != $this->old_filename; }, null);
		$this->AddAttribute('content', function() { return $this->content; }, function($value) { $this->content = $value; });
	}

	public function Save()
	{
		$this->archive->SetFile($this);
	}

	public function Validate()
	{
		return true;
	}
}

?>
