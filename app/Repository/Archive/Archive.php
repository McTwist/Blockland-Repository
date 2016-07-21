<?php
namespace App\Repository\Archive;

/*
|--------------------------------------------------------------------------
| Archive
|--------------------------------------------------------------------------
|
| Handles an archive and all its content. Applies easy functionality to
| access each file. Parsing and modification is done through ArchiveFile
|
*/
class Archive
{
	protected $archive_name = '';
	private $archive = null;

	private $filetype_count = [];

	private $filetype_readers = [];
	private $file_readers = [];

	// Convenient common newline
	const NL = "\r\n";

	public function __construct($file)
	{
		$this->archive_name = $file;
		// Read archive
		$this->archive = new \ZipArchive();
		if ($this->archive->open($file) !== true)
		{
			$this->archive = null;
			return;
		}

		$this->CountFileTypes();
	}

	public function IsOpen()
	{
		return $this->archive !== null;
	}

	public function Close()
	{
		$this->archive->close();
	}

	// Add file reader
	// file - File to find
	// reader - Class name for reader
	public function AddFileReader($file, $reader)
	{
		if (!empty($file) && class_exists($reader) && is_subclass_of($reader, ArchiveFile::class))
		{
			if (is_array($file))
			{
				$files = array_map('strtolower', $file);
				foreach($files as $file)
				{
					$this->file_readers[$file] = $reader;
				}
			}
			else
			{
				$file = strtolower($file);
				$this->file_readers[$file] = $reader;
			}
		}
	}

	// Add file type reader
	// type - Extension of the file type
	// reader - Class name for reader
	public function AddFileTypeReader($type, $reader)
	{
		if (!empty($type) && class_exists($reader) && is_subclass_of($reader, ArchiveFile::class))
		{
			if (is_array($type))
			{
				$types = array_map('strtolower', $type);
				foreach($types as $type)
				{
					$this->filetype_readers[$type] = $reader;
				}
			}
			else
			{
				$type = strtolower($type);
				$this->filetype_readers[$type] = $reader;
			}
		}
	}

	// Get file to read
	public function GetFile($file, $force_object = false)
	{
		$file = strtolower($file);
		if (!$this->HaveFile($file) && !$force_object)
			return null;

		$content = $this->ReadFile($file);

		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		$reader = ArchiveFile::class;

		// Read a file
		if (array_key_exists($file, $this->file_readers))
		{
			$reader = $this->file_readers[$file];
		}
		// Read a type
		elseif (array_key_exists($ext, $this->filetype_readers))
		{
			$reader = $this->filetype_readers[$ext];
		}
		elseif (!$force_object)
		{
			return $content;
		}
		
		$reader = new $reader($this->archive_name, $file);
		$reader->Read($content);
		return $reader;
	}

	// Set file to archive
	public function SetFile($obj)
	{
		if ($obj instanceof ArchiveFile)
			$this->WriteFile($obj->Filename(), $obj->Write());
	}

	// Validates file to contain the required data
	public function Validate()
	{
		return true;
	}

	// Get out all those pesky files that somehow get into every other add-on
	public function Cleanup()
	{
		$this->RemoveFile('Thumbs.db'); // Windows thumbnails
		$this->RemoveFile('.DS_Store'); // Mac folder attributes
		$this->RemoveFile('.svn'); // SVN
		$this->RemoveFile('.git'); // GIT
		$this->RemoveFile('.gitignore'); // GIT ignore
	}

	protected function RemoveFile($file)
	{
		$found = false;
		while (($index = $this->archive->locateName($file, \ZipArchive::FL_NODIR | \ZipArchive::FL_NOCASE)) !== false)
			$found |= $this->archive->deleteIndex($index);
		$this->CountFileTypes();
		return $found;
	}

	protected function HaveFile($file)
	{
		return $this->archive->locateName($file, \ZipArchive::FL_NOCASE | \ZipArchive::FL_NODIR) !== false;
	}

	protected function HaveFolder($folder)
	{
		return !$this->HaveFile($folder) && $this->archive->locateName($folder, \ZipArchive::FL_NOCASE) !== false;
	}

	protected function HasFileType($ext)
	{
		return isset($this->filetype_count[strtolower($ext)]);
	}

	protected function ReadFile($file)
	{
		return $this->archive->getFromName($file, 0, \ZipArchive::FL_NOCASE);
	}

	// Write to file if not empty
	protected function WriteFile($file, $content)
	{
		if (!empty($content))
		{
			$this->archive->addFromString($file, $content);
			$this->CountFileTypes();
		}
	}

	// Count all file types
	private function CountFileTypes()
	{
		$this->filetype_count = [];
		for ($i = 0; $i < $this->archive->numFiles; $i++)
		{
			$stat = $this->archive->statIndex($i);
			$values = explode('.', $stat['name']);
			$ext = strtolower(end($values));

			if (!isset($this->filetype_count[$ext]))
				$this->filetype_count[$ext] = 0;

			++$this->filetype_count[$ext];
		}
	}
}

?>